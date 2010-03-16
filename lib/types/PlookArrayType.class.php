<?php

class PlookArrayType extends PlookBaseType implements ArrayAccess, Iterator
{
  public function __construct($data, $type)
  {
    $data = ltrim(rtrim($data, '}'), '{');
    $data_array = split(',', $data);

    $this->data = array();
    foreach ($data_array as $data)
    {
      $this->data[] = new $type($data);
    }
  }

  public function __toString()
  {
    throw new PlookException(sprintf('Cannot cast type Array into String.'));
  }

  public function toPg()
  {
    $string = array();
    foreach($this->data as $data)
    {
      $string[] = $data->toPg();
    }

    return sprintf('{%s}', join(',', $string));
  }

  public function offsetSet($offset, $value) 
  {
    $this->data[$offset] = $value;
  }
  public function offsetExists($offset) 
  {
    return isset($this->data[$offset]);
  }
  public function offsetUnset($offset)
  {
    unset($this->data[$offset]);
  }
  public function offsetGet($offset) 
  {
    return isset($this->data[$offset]) ? $this->data[$offset] : null;
  }

  function rewind()
  {
    rewind($this->data);
  }

  function current()
  {
    return current($this->data);
  }

  function key()
  {
    return key($this->data);
  }

  function next()
  {
    next($this->data);
  }

  function valid()
  {
    return current($this->data) ? TRUE : FALSE;
  }
}
