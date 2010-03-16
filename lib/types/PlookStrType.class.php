<?php

class PlookStrType extends PlookBaseType
{
  public function toPg()
  {
    return "'$data'";
  }
}
