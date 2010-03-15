<?php

abstract class PlookBaseObject
{
  const NONE     = 0;
  const EXIST    = 1;

  protected $fields = array();
  protected $status = 0;
  protected $primary_key = array();

  public function __construct(Array $pk = array())
  {
    $this->setPrimaryKey($pk);
  }

  public function get($var)
  {
    return $this->fields[$var];
  }

  public function has($var)
  {
    return array_key_exists($var, $this->fields);
  }

  public function __call($method, $arguments)
  {
    $operation = substr(strtolower($method), 0, 3);
    $attribute = sfInflector::underscore(substr($method, 3));

    switch($operation)
    {
      case 'set':
        return $this->set($attribute, $arguments[0]);
        break;
      case 'get':
        return $this->get($attribute);
        break;
      default:
        throw new PlookException(sprintf('No such method "%s:%s()"', get_class($this), $method));
    }
  }

  public function hydrate(Array $values)
  {
    $this->fields = $values;
    $this->status = self::EXIST;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function getFields()
  {
    return $this->fields;
  }

  public function setPrimaryKey(Array $keys)
  {
    $this->primary_key = $keys;
  }

  public function getPrimaryKey()
  {
    $keys = array();
    foreach ($this->primary_key as $key)
    {
      $keys[$key] = array_key_exists($key, $this->fields) ? $this->fields[$key] : null;
    }

    return $keys;
  }
}
