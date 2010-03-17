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

  public function add($var, $value)
  {
    if (preg_match('/array/i', $this->fields_definition[$var]))
    {
      if ($this->has($var) && is_array($this->fields[$var]))
      {
        $this->fields[$var][] = $value;
      }
      else
      {
        $this->fields[$var] = array($value);
      }
    }
    else
    {
      throw new PlookException(sprintf('"%s" field is not an array.', $var));
    }
  }
}
