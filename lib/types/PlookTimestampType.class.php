<?php

class PlookTimestampType extends PlookBaseType
{
  public function __construct($data)
  {
    $this->data = new DateTime($data);
  }

  public function __toString()
  {
    return $this->data->format('U');
  }

  public function toPg()
  {
    return $this->data->format('c');
  }

  public function getTimestamp()
  {
    return $this->data;
  }
}
