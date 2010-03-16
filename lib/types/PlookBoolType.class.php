<?php

class PlookBoolType extends PlookBaseType
{
  public function __construct($data)
  {
    $this->data = ($data == 'true');
  }

  public function toPg()
  {
    return $this->data ? 'true' : 'false';
  }
}
