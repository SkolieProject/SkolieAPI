<?php

namespace MInuz\SkolieAPI\repo;

use Minuz\SkolieAPI\config\connection\ConnectionCreator;

use Minuz\SkolieAPI\model\Account\Student\Student;
use Minuz\SkolieAPI\model\Account\Teacher\Teacher;
use Minuz\SkolieAPI\model\Role\Role;

class AccountRepository
{

  private \PDO $pdo;
  public bool $exit_code;
  public Teacher|Student $account;

  public function __construct()
  {
    $this->pdo = ConnectionCreator::connect();
  }




  public function enter(string $email, string $password): self
  {
    $stmt = $this->pdo->prepare(self::QUERY_GET_ROLE);

    $stmt->execute([
      ':email' => $email,
      ':password' => $password
    ]);

    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($data === false) {

      $this->exit_code = true;
      return $this;
    }

    $this->exit_code = false;

    if ($data['role'] == 'STDNT') {

      $this->account = $this->studentEnter($data['id']);
      return $this;
    }

    if ($data['role'] == 'TCHR') {

      $this->account = $this->teacherEnter($data['id']);
      return $this;
    }
  }




  private function teacherEnter(int $id): Teacher
  {
    $stmt = $this->pdo->prepare(self::QUERY_ENTER_TEACHER);
    $stmt->execute([
      ':id' => $id
    ]);

    $data = $stmt->fetch(\PDO::FETCH_ASSOC);
    $acc = new Teacher($id, $data['name'], $data['email'], $data['subject_name']);

    $stmt = $this->pdo->prepare(self::QUERY_GET_TEACHER_CLASSES);
    $stmt->execute([':id' => $id]);

    while ($class_data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $acc->addClass($class_data['class']);
    }
    return $acc;
  }





  private function studentEnter(int $id): Student
  {
    $stmt = $this->pdo->prepare(self::QUERY_ENTER_STUDENT);
    $stmt->execute([
      ':id' => $id
    ]);

    $data = $stmt->fetch(\PDO::FETCH_ASSOC);
    $acc = new Student($id, $data['name'], $data['email'], $data['class']);
    return $acc;
  }



  public function create(string $email, string $password, array $userinfo): self
  {
    if ($userinfo['role'] == Role::Student) {
      $stmt = $this->pdo->prepare(self::QUERY_CREATE_STUDENT);
      $stmt->execute([
        ':email' => $email,
        ':password' => $$password,
        ':name' => $userinfo['name'],
        ':class' => $userinfo['class']
      ]);

      $data = $stmt->fetch(\PDO::FETCH_ASSOC);
      $this->account = $this->enter($email, $password);
      return $this;
    }

    if ($userinfo['role'] == Role::Teacher) {
      $stmt = $this->pdo->prepare(self::QUERY_CREATE_TEACHER);
      $stmt->execute([
        ':email' => $email,
        ':password' => $$password,
        ':name' => $userinfo['name'],
        ':subject_name' => $userinfo['subject']
      ]);

      $stmt = $this->pdo->prepare(self::QUERY_INSERT_TEACHER_CLASSES);
      $stmt->bindParam(':class', $next_class);
      foreach ($userinfo['classes'] as $class) {
        try {
          $next_class = $class;
          $stmt->execute();
        } catch (\PDOException $e) {
          $this->exit_code = true;
          return $this;
        }
      }

      $this->account = $this->enter($email, $password);
      return $this;
    }
  }




  private const QUERY_GET_ROLE =
  "
    SELECT role, id
    FROM skolie_db.User AS u
      WHERE u.email = :email
      AND u.password = :password
    ;
  ";

  private const QUERY_ENTER_TEACHER =
  "
    SELECT u.name, t.subject_name, u.email FROM 
    	skolie_db.Teacher AS t
      INNER JOIN skolie_db.User AS u
      ON u.id = t.id
    WHERE t.id = :id
    ;
  ";


  private const QUERY_ENTER_STUDENT =
  "
    SELECT u.name, s.class, u.email FROM 
	    skolie_db.Student AS s
      INNER JOIN skolie_db.User AS u
      ON u.id = s.id
    WHERE s.id = :id
    ;
  ";



  private const QUERY_CREATE_STUDENT =
  "
    INSERT INTO skolie_db.User(email, password, name, role)
      VALUES
      (
        :email, :password, :name, 'STDNT'
      )
    ;
    INSERT INTO skolie_db.Student(id, class)
      VALUES
      (
        LAST_INSERT_ID(), :class
      )
    ;
  ";



  private const QUERY_CREATE_TEACHER =
  "
    INSERT INTO skolie_db.User(email, password, name, role)
    	VALUES
	    (
		    :email, :password, :name, TCHR
	    )
    ;

    INSERT INTO skolie_db.Teacher(id, class, subject_name)
      VALUES
        (
        LAST_INSERT_ID(), :class, :subject_name
        )
    ;

  ";


  private const QUERY_INSERT_TEACHER_CLASSES =
  "
    INSERT INTO skolie_db.Teacher_to_Class(teacher, class)
      VALUES
      (
        LAST_INSERT_ID(), :class
      )
    ;
  ";


  private const QUERY_GET_TEACHER_CLASSES =
  "
    SELECT class FROM
      skolie_db.Teacher_to_Class AS ttc
    WHERE
      ttc.teacher = :id

    ;
  ";
}
