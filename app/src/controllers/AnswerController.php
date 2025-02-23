<?php

namespace MInuz\SkolieAPI\controllers;

use Minuz\SkolieAPI\repo\AnswerRepository;

class AnswerController
{

  private AnswerRepository $answer_repository;

  public function __construct()
  {
    $this->answer_repository = new AnswerRepository();
  }
}
