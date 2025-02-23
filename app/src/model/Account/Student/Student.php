<?php

namespace Minuz\SkolieAPI\model\Account\Student;

use Minuz\SkolieAPI\model\Account\Account;

final class Student extends Account
{

  public function __construct(
    int $id,
    string $name,
    string $email,
    private string $class
  ) {
    parent::__construct($id, $name, $email, 'STDNT');
  }

  public function overview(): array
  {
    return [
      'name' => $this->name,
      'class' => $this->class
    ];
  }
}
