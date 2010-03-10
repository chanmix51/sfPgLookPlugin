<?php

abstract class PlookBaseObjectMap
{
  protected $connection;
  protected $object_class;
  protected $object_name;
  protected $field_names = array();


  abstract public function getObjectClass();
  abstract protected function getObjectName();
  abstract protected function getFieldNames();
  abstract protected function initialize();

  public function __construct()
  {
    $this->initialize();

    if (is_null($this->connection))
    {
      throw new PlookException(sprintf('PDO connection not set after initializing db map "%s".', get_class($this)));
    }
    if (is_null($this->object_class))
    {
      throw new PlookException(sprintf('Missing object_class after initializing db map "%s".', get_class($this)));
    }
    if (count($field_names) == 0)
    {
      throw new PlookException(sprintf('No fields after initializing db map "%s", don\'t you prefer anonymous objects ?', get_class($this)));
    }
  }

  protected function prepareStatement($sql)
  {
    return $this->connection->prepare($sql);
  }

  protected function doQuery(PDOStatement $stmt, $values)
  {
    foreach ($values as $pos => $value)
    {
      if (is_integer($value))
      {
        $type = PDO::PARAM_INT;
      }
      elseif (is_bool($value))
      {
        $type = PDO::PARAM_BOOL;
      }
      else
      {
        $type = PDO::PARAM_STR;
      }

      $stmt->bindValue($pos + 1, $value, $type);
    }
    
    try
    {
      return $stmt->execute();
    }
    catch(PDOException $e)
    {
      throw new PlookException('Error while performing SQL query «%s». The driver said "%s".', $sql, $e->getMessage());
    }
  }

  public function query($sql, $values)
  {
    $stmt = $this->prepareStatement($sql);

    return $this->doQuery($stmt, $values);
  }

  public function queries($sql, $value_set)
  {
    $stmt = $this->prepareStatement($sql);
    $stmts = array();
    foreach($value_set as $values)
    {
      $stmt = $this->doQuery($stmt, $values);
      $stmts[] = clone $stmt;
      $stmt->closeCursor();
    }

    return $stmts;
  }
}
