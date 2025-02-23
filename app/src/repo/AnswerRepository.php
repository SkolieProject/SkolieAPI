<?php

namespace Minuz\SkolieAPI\repo;

use Minuz\SkolieAPI\config\connection\ConnectionCreator;
use Minuz\SkolieAPI\model\Answer\Answer;
use Minuz\SkolieAPI\model\Role\Role;

class AnswerRepository
{
  private \PDO $pdo;

  public $exit_code;
  public Role $user_role;

  public Answer $answer;
  public array $answers;

  public function __construct()
  {
    $this->pdo = ConnectionCreator::connect();
  }
}
