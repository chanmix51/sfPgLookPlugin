<?php

abstract class PlookBaseType
{
  public static function toPg($data)
  {
    return $data;
  }

  public static function fromPg($data)
  {
    return $data;
  }
}
