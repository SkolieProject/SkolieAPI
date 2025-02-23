<?php

namespace Minuz\SkolieAPI\model\Assay\Question\Alternative;


class Alternative
{
  public function __construct(
    private string $label,
    private string $alternative_text
  ) {}


  public function overview(): array
  {
    $overview = [
      'Label' => $this->label,
      'Description' => $this->alternative_text
    ];

    return $overview;
  }
}
