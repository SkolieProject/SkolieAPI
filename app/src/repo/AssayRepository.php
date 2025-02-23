<?php

namespace Minuz\SkolieAPI\repo;

use DateTimeImmutable;
use Minuz\SkolieAPI\config\connection\ConnectionCreator;
use Minuz\SkolieAPI\model\Answer\Answer;
use Minuz\SkolieAPI\model\Assay\Assay;
use Minuz\SkolieAPI\model\Assay\Question\Alternative\Alternative;
use Minuz\SkolieAPI\model\Assay\Question\Question;
use Minuz\SkolieAPI\model\Role\Role;

class AssayRepository
{
  private \PDO $pdo;

  public $exit_code;
  public Role $user_role;

  public Assay $assay;
  public array $assay_group;

  public function __construct()
  {
    $this->pdo = ConnectionCreator::connect();
  }


  public function newAssay(int $user_id, array $assay_info): self
  {

    $stmt = $this->pdo->prepare(self::QUERY_NEW_ASSAY);

    $deadline = new DateTimeImmutable($assay_info['deadline']);
    $stmt->execute([
      ':teacher' => $user_id,
      ':class' => $assay_info['class'],
      ':title' => $assay_info['title'],
      ':deadline' => $deadline->format('Y-m-d')
    ]);
    $assay_id = $this->pdo->lastInsertId();
    unset($stmt);

    if (empty($assay_info['questions'])) {

      $this->assay = $this->getAssay($user_id, $assay_id);
      return $this;
    }

    $this->writeQuestions($user_id, $assay_id, $assay_info['questions']);
    return $this;
  }


  public function writeQuestions(int $user_id, int $assay_id, array $questions): self
  {

    $stmt = $this->pdo->prepare(self::QUERY_ERASE_QUESTIONS);
    foreach ($questions as $question)
      $stmt->execute([
        ':assay_id' => $assay_id,
        ':number' => $question['number']
      ]);

    $stmt_questions = $this->pdo->prepare(self::QUERY_WRITE_QUESTIONS);
    $stmt_alternatives = $this->pdo->prepare(self::QUERY_WRITE_ALTERNATIVES);

    foreach ($questions as $question) {
      $stmt_questions->execute([
        ':assay_id' => $assay_id,
        ':number' => $question['number'],
        ':question_text' => $question['description'],
        ':correct_answer' => $question['correct_answer']
      ]);
      $question_id = $this->pdo->lastInsertId();
      foreach ($question['alternatives'] as $alternative) {
        $stmt_alternatives->bindvalue(':question_id', $this->pdo->lastInsertId());
        $stmt_alternatives->execute([
          ':question_id' => $question_id,
          ':label' => $alternative['label'],
          ':alternative_text' => $alternative['alternative_text']
        ]);
      }
    }

    $this->exit_code = false;
    $this->getAssay($user_id, $assay_id);

    return $this;
  }


  public function getAssay(int $user_id, int $assay_id): self
  {
    if ($this->canAcess($user_id, $assay_id, $user_role) == false) {
      $this->exit_code = true;
      return $this;
    }

    $stmt_assay_header = $this->pdo->prepare(self::QUERY_GET_ASSAY_HEADER);
    $stmt_assay_questions = $this->pdo->prepare(self::QUERY_GET_ASSAY_QUESTIONS);
    $stmt_assay_alternatives = $this->pdo->prepare(self::QUERY_GET_ASSAY_ALTERNATIVES);

    $stmt_assay_header->execute([
      ':assay_id' => $assay_id
    ]);

    $assay_header_data = $stmt_assay_header->fetch(\PDO::FETCH_ASSOC);


    $assay = new Assay(
      $assay_id,
      $user_role,
      $assay_header_data['teacher'],
      $assay_header_data['class'],
      $assay_header_data['title'],
      $assay_header_data['deadline']
    );

    $stmt_assay_questions->execute([':assay_id' => $assay_id]);
    foreach ($stmt_assay_questions->fetchAll(\PDO::FETCH_ASSOC) as $question_data) {

      $question = new Question(
        $question_data['question_text'],
        $question_data['correct_answer']
      );

      $stmt_assay_alternatives->execute([':question_id' => $question_data['id']]);

      while ($alternative_data = $stmt_assay_alternatives->fetch(\PDO::FETCH_ASSOC)) {

        $alternative = new Alternative(
          $alternative_data['label'],
          $alternative_data['alternative_text']
        );

        $question->addAlternative($alternative);
      }

      $assay->addQuestion($question);
    }

    $this->exit_code = false;
    $this->assay = $assay;

    return $this;
  }




  public function getTeacherAssays(int $teacher_id): self
  {
    $stmt = $this->pdo->prepare(self::QUERY_GET_ASSAYS_TEACHER);

    $stmt->execute([
      ':teacher_id' => $teacher_id
    ]);

    while ($assay_data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $assay = new Assay(
        $assay_data['id'],
        Role::Teacher,
        $assay_data['teacher'],
        $assay_data['class'],
        $assay_data['title'],
        $assay_data['deadline']
      );

      $this->assay_group[] = $assay;
    }
    return $this;
  }




  public function getAssaysFromClass() {}





  public function checkRole(int $user_id): self
  {
    $stmt = $this->pdo->prepare(self::QUERY_GET_ROLE);
    $stmt->execute([
      ':id' => $user_id
    ]);

    $role_data = $stmt->fetch(\PDO::FETCH_ASSOC);
    $this->user_role = Role::translate($role_data['role']);
    return $this;
  }


  public function canAcess(int $user_id, int $assay_id, &$role): bool
  {
    $stmt_role = $this->pdo->prepare(self::QUERY_GET_ROLE);
    $stmt_role->execute([':id' => $user_id]);
    $role_data = $stmt_role->fetch(\PDO::FETCH_ASSOC);
    $role = $role_data['role'];

    $role = Role::translate($role);
    if ($role === Role::Teacher) {
      return true;
    }

    $stmt_visiblility = $this->pdo->prepare(self::QUERY_GET_VISIBILITY);
    $stmt_visiblility->execute([':assay_id' => $assay_id]);
    $visiblity_data = $stmt_visiblility->fetch(\PDO::FETCH_ASSOC);

    return $visiblity_data['is_visible'];
  }


  private const QUERY_GET_ROLE =
  "
    SELECT role
    FROM skolie_db.User AS u
      WHERE u.id = :id
    ;
  ";


  private const QUERY_GET_VISIBILITY =
  "
    SELECT a.is_visible
    FROM skolie_db.Assay AS a
      WHERE a.id = :assay_id
    ;
  ";



  private const QUERY_NEW_ASSAY =
  "
    INSERT INTO skolie_db.Assay(teacher, class, title, deadline)
  	  VALUES
      (
  		  :teacher, :class, :title, :deadline
      )
    ;

    SELECT LAST_INSERT_ID() AS id; 
  ";



  private const QUERY_ERASE_QUESTIONS =
  "
    DELETE FROM skolie_db.Question AS q
    WHERE q.assay_id = :assay_id
    AND q.number = :number
    ;
  ";



  private const QUERY_WRITE_QUESTIONS =
  "
    INSERT INTO skolie_db.Question(assay_id, number, question_text, correct_answer)
      VALUES
      (
        :assay_id, :number, :question_text, :correct_answer
      )
    ;
  ";



  private const QUERY_WRITE_ALTERNATIVES =
  "
    INSERT INTO skolie_db.Alternative(question_id, alternative_text, label)
      VALUES
      (
        :question_id, :alternative_text, :label
      )
    ;
  ";


  private const QUERY_GET_ASSAY_HEADER =
  "
    SELECT a.title, a.class, a.deadline, u.name AS teacher
    FROM skolie_db.Assay AS a	
	    INNER JOIN skolie_db.User AS u
	  ON u.id = a.teacher
    WHERE 
	    a.id = :assay_id
    ;
  ";



  private const QUERY_GET_ASSAY_QUESTIONS =
  "
    SELECT q.id, q.question_text, q.correct_answer
      FROM skolie_db.Question AS q	
    WHERE 
	  q.assay_id = :assay_id
    ORDER BY q.number
    ;
  ";



  private const QUERY_GET_ASSAY_ALTERNATIVES =
  "
    SELECT alt.label, alt.alternative_text
      FROM skolie_db.Alternative AS alt	
    WHERE 
	    alt.question_id = :question_id
    ;
  ";



  private const QUERY_GET_ASSAYS_TEACHER =
  "
    SELECT 
      a.id,
      a.title,
      u.name AS teacher,
      a.deadline,
      a.class
    FROM skolie_db.Assay AS a
      INNER JOIN skolie_db.User AS u
        ON u.id = a.teacher
      INNER JOIN skolie_db.Teacher_to_Class AS TtC
        ON TtC.teacher = :teacher_id
    WHERE a.class = TtC.class
    ;
  ";
}
