<?php

abstract class PlookBaseType
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function toPg()
  {
    return $data;
  }

  public function __toString()
  {
    return $data;
  }
}
