<?php

namespace Minuz\SkolieAPI\model\Role;

enum Role: string
{

  case Student = 'STDNT';

  case Teacher = 'TCHR';

  public static function translate(string $role)
  {
    return match ($role) {
      'TCHR' => self::Teacher,
      'STDNT' => self::Student,
    };
  }

  public static function roleType(string $role)
  {
    return match ($role) {
      'TCHR' => 'Teacher',
      'STDNT' => 'Stduent'
    };
  }
}
