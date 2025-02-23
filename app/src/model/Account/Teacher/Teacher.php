<?php

namespace Minuz\SkolieAPI\model\Account\Teacher;

use Minuz\SkolieAPI\model\Account\Account;

class Teacher extends Account
{
  private array $classes;

  public function __construct(
    int $id,
    string $name,
    string $email,
    private string $subject_name,
  ) {
    parent::__construct($id, $name, $email, 'TCHR');
  }

  public function overview(): array
  {
    return [
      'name' => $this->name,
      'subject' => $this->subject_name,
      'classes' => $this->classes
    ];
  }


  public function addClass(string $class)
  {
    $this->classes[] = $class;
  }
}
