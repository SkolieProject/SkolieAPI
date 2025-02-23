<?php

namespace Minuz\SkolieAPI\model\Account;

use Minuz\SkolieAPI\model\Role\Role;

abstract class Account
{
  private string $role;

  public function __construct(
    public int $id,
    protected string $name,
    protected string $email,
    string $role
  ) {
    $this->role = Role::roleType($role);
  }

  abstract public function overview();
}
