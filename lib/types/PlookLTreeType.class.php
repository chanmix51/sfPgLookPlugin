<?php

class PlookLTreeType extends PlookBaseType
{
  public function __construct($data)
  {
    $this->data = split('\.', $data);
  }

  public function __toString()
  {
    return join(' ', $this->data);
  }

  public function toPg()
  {
    return sprintf("'%s'", join('.', $data));
  }
}
