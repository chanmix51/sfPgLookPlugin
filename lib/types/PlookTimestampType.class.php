<?php

class PlookTimestampType extends PlookBaseType
{
  public static function fromPg($data)
  {
    return new DateTime($data);
  }

  public static function toPg($data)
  {
    if (!$data instanceof DateTime)
    {
      $data = new DateTime($data);
    }

    return $data->format('c');
  }
}
