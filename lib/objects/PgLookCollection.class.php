<?php

class PgLookCollection implements ArrayAccess, Iterator, Countable 
{
  protected $collection = array();
  protected $position;

  public function __construct(Array $data)
  {
    $this->collection = $data;
    $this->position = $this->count() > 0 ? 0 : null;
  }

  public function count()
  {
    return count($this->collection);
  }

  public function addData($data)
  {
    $this->collection[] = $data;
  }

  public function offsetSet($offset, $value) 
  {
    $this->collection[$offset] = $value;
  }

  public function offsetExists($offset) 
  {
    return isset($this->collection[$offset]);
  }

  public function offsetUnset($offset) 
  {
    unset($this->collection[$offset]);
  }

  public function offsetGet($offset) 
  {
    return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
  }

  public function rewind() 
  {
    $this->position = 0;
  }

  public function current() 
  {
    return $this->collection[$this->position];
  }

  public function key() 
  {
    return $this->position;
  }

  public function next() 
  {
    ++$this->position;
  }

  public function valid() 
  {
    return isset($this->collection[$this->position]);
  }

  public function isFirst()
  {
    return $this->position == 0;
  }

  public function isLast()
  {
    return $this->position == $this->count() - 1;
  }

  public function isEmpty()
  {
    return is_null($this->position);
  }

  public function isEven()
  {
    return ($this->position % 2) == 0;
  }

  public function isOdd()
  {
    return ($this->position % 2) == 1;
  }

  public function getOddEven()
  {
    return $this->position % 2 ? 'odd' : 'even';
  }
}
