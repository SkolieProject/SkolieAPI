<?php

namespace Minuz\SkolieAPI\model\Assay;

use DateTimeImmutable;
use Minuz\SkolieAPI\model\Assay\Question\Question;
use Minuz\SkolieAPI\model\Role\Role;

class Assay
{

  private array $questions;
  private DateTimeImmutable $deadline;

  public function __construct(
    private int $id,
    private Role $role_viewing,
    private string $teacher,
    private string $class,
    private string $title,
    string $deadline,
  ) {

    $this->deadline = new DateTimeImmutable($deadline);
  }


  public function addQuestion(Question $question)
  {
    $this->questions[] = $question;
  }


  public function overview(): array
  {
    $questions = [];


    if (empty($this->questions) == false) {

      foreach ($this->questions as $question) {
        $questions[] = $question->overview($this->role_viewing);
      }
    }

    return [
      'Teacher' => $this->teacher,
      'Class' => $this->class,
      'Title' => $this->title,
      'Deadline' => $this->deadline->format("d/m/Y"),
      'Questions' => $questions
    ];
  }
}
