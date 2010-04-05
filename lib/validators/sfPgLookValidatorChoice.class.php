<?php

class sfPgLookValidatorChoice extends sfValidatorBase
{
  public function configure($options = array(), $errors = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('method');
    $this->addoption('column');
  }

  protected function doClean($value)
  {
    $map_class = PgLook::getMapFor($this->getOption('model'));

    $method = $this->getOption('method');
    if (!method_exists($map_class, $method))
    {
      throw new PgLookException(sprintf('Class "%s" does not have a "%s" method.', get_class($map_class), $method));
    }

    $column = $this->getOption('column');
    if (!$map_class->hasField($column))
    {
      throw new PgLookException(sprintf('Table "%s" has no such column "%s".', $map_class->getTableName(), $column));
    }

    $result = call_user_func(array($map_class, $method), array($column => $value));

    if (($result instanceof PgLookCollection and $result->isEmpty())
      or
       (!$result instanceOf PgLookBaseObject))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    return $value;
  }
}

