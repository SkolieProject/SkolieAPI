<?php

namespace Minuz\SkolieAPI\model\Answer;


class Answer
{
  private array $answers;

  public function __construct(
    array $questions,
    array $choosed_alternatives
  ) {
    $this->answers = array_combine($questions, $choosed_alternatives);
  }


  public function overview(): array
  {
    return $this->answers;
  }
}
