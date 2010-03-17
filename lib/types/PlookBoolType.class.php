<?php

class PlookBoolType extends PlookBaseType
{
  public static function fromPg($data)
  {
    return ($data == 't');
  }

  public static function toPg($data)
  {
    return $data ? "'true'" : "'false'";
  }
}
