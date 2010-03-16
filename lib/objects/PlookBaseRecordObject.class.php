<?php

class PlookBaseRecordObject extends PlookBaseObject
{
  const MODIFIED = 2;

  public function __set($var, $value)
  {
    $this->set($var, $value);
  }

  public function set($var, $value)
  {
    $this->fields[$var] = $value;
    $this->status = $this->status | self::MODIFIED;
  }
}
