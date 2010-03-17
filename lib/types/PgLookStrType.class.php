<?php

class PgLookStrType extends PgLookBaseType
{
  public static function toPg($data)
  {
    $data = str_replace("'", "''", $data);

    return "'$data'";
  }
}
