<?php

namespace Minuz\SkolieAPI\model\Assay\Question;

use Minuz\SkolieAPI\model\Assay\Question\Alternative\Alternative;
use Minuz\SkolieAPI\model\Role\Role;

class Question
{
  private array $alternatives;

  public function __construct(
    private string $question_text,
    private string $correct_answer,
  ) {}


  public function addAlternative(Alternative $alternative)
  {
    $this->alternatives[] = $alternative;
  }

  public function overview(Role $role): array
  {
    $alternatives = [];

    if (empty($this->alternatives) == false) {
      foreach ($this->alternatives as $alternative) {
        $alternatives[] = $alternative->overview();
      }
    }
    $question_overview = [
      'Description' => $this->question_text,
      'Alternatives' => $alternatives
    ];

    if ($role == Role::Teacher) {
      $question_overview["Answer"] = $this->correct_answer;
    }

    return $question_overview;
  }
}
