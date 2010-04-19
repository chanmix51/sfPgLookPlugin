<?php

abstract class PgLookForm extends PgLookBaseForm
{
  protected $object;
  protected $is_new = true;

  abstract protected function getClassName();

  public function __construct(PgLookBaseObject $object = null, $options = array(), $CSRFSecret = null)
  {
    $class_name = $this->getClassName();
    if (is_null($object))
    {
      $this->object = PgLook::getMapFor($this->getClassName())->createObject();
    }
    elseif (!$object instanceOf $class_name)
    {
      throw new PgLookSqlException(sprintf('"%s" forms can only be fed with a "%s" object ("%s" given).', get_class($this), $class_name, get_class($object)));
    }
    else
    {
      $this->object = $object;
    }

    parent::__construct($this->object->getFields(), $options, $CSRFSecret);
  }

  public function bindAndSave(Array $tainted_values = array(), Array $tainted_files = array())
  {
    $this->bind($tainted_values, $tainted_files);
    if ($this->isValid())
    {
      $this->processValues();
      PgLook::getMapFor($this->getClassName())->saveOne($this->object);

      return $this->object;
    }

    return false;
  }

  public function getObject()
  {
    return $this->object;
  }

  protected function processValues()
  {
    $values = array();
    foreach($this->getValues() as $field => $value)
    {
      $method = sprintf('process%s', sfInflector::camelize($field));
      if (method_exists($this, $method))
      {
        $ret = call_user_func(array($this, $method), array($value));
        if (!is_null($ret))
        {
          $values[$field] = $ret;
        }
      }
      else
      {
        $values[$field] = $value;
      }
    }

    $this->object->hydrate($values);
    if (!$this->isNew())
    {
      $this->object->setStatus(PgLookBaseObject::EXIST);
    }

  }

  public function isNew()
  {
    return $this->is_new;
  }

  public function setNew($new)
  {
    $this->is_new = (boolean) $new;
  }

}
