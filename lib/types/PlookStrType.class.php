<?php

class PlookStrType extends PlookBaseType
{
  public static function toPg($data)
  {
    $data = str_replace("'", "''", $data);

    return "'$data'";
  }
}
