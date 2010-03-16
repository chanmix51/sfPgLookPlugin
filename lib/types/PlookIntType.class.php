<?php

class PlookIntType extends PlookBaseType
{
  public static function fromPg($data)
  {
    return $data;
  }

  public static function toPg($data)
  {
    return $data;
  }
}
