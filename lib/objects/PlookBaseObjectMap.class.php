<?php

abstract class PlookBaseObjectMap
{
  protected $connection;
  protected $object_class;
  protected $object_name;
  protected $field_names = array();
  protected $pk_fields = array();

  abstract protected function initialize();

  public function createObject()
  {
    $class_name = $this->object_class;

    return new $class_name($this->pk_fields);
  }

  public function getFieldNames()
  {
    return $this->field_names;
  }

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
    if (count($this->field_names) == 0)
    {
      throw new PlookException(sprintf('No fields after initializing db map "%s", don\'t you prefer anonymous objects ?', get_class($this)));
    }
  }

  protected function prepareStatement($sql)
  {
    return $this->connection->getPdo()->prepare($sql);
  }

  protected function bindParams($stmt, $values)
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
        $type = null;
      }

      if (is_null($type))
      {
        $stmt->bindValue($pos + 1, $value);
      }
      else
      {
        $stmt->bindValue($pos + 1, $value, $type);
      }
    }

    return $stmt;
  }

  protected function doQuery($sql, $values)
  {
    $stmt = $this->prepareStatement($sql);
    $this->bindParams($stmt, $values);
    try
    {
      $stmt->execute();

      return $stmt->rowCount() ? $this->createObjectsFromStmt($stmt) : null;
    }
    catch(PDOException $e)
    {
      throw new PlookException('Error while performing SQL query «%s». The driver said "%s".', $sql, $e->getMessage());
    }
  }

  public function query($sql, $values)
  {
    return $this->doQuery($sql, $values);
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

  protected function createSqlAndFrom($values)
  {
    $sql = array();
    foreach ($values as $key => $value)
    {
      $sql[] = "$key = ?";
    }

    return join(' AND ', $sql);
  }

  protected function createObjectsFromStmt(PDOStatement $stmt)
  {
    $objects = array();
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $values)
    {
      $object = $this->createObject();
      $object->hydrate($values);

      $objects[] = $object;
    }

    return $objects;
  }

  public function findAll()
  {
    return $this->query(sprintf('SELECT * FROM %s;', $this->object_name), array());
  }

  public function findWhere($where, $values)
  {
    return $this->query(sprintf('SELECT * FROM %s WHERE %s;', $this->object_name, $where), $values);
  }

  public function getPrimaryKey()
  {
    return $this->pk_fields;
  }

  public function findByPk(Array $values)
  {
    if (count(array_diff(array_keys($values), $this->getPrimaryKey())) != 0)
    {
      throw new PlookException(sprintf('Given values "%s" do not match PK definition "%s" using class "%s".', print_r($values, true), print_r($this->getPrimaryKey(), true), get_class($this)));
    }

    $result = $this->findWhere($this->createSqlAndFrom($values), array_values($values));

    return count($result) == 1 ? $result[0] : null;
  }
}
