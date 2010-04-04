<?php

abstract class PgLookBaseObjectMap
{
  protected $connection;
  protected $object_class;
  protected $object_name;
  protected $field_definitions = array();
  protected $pk_fields = array();

  abstract protected function initialize();

  protected function addField($name, $type)
  {
    if (array_key_exists($name, $this->field_definitions))
    {
      throw new PgLookException(sprintf('Field "%s" already set in class "%s".', $name, get_class($this)));
    }

    $this->field_definitions[$name] = $type;
  }

  public function createObject()
  {
    $class_name = $this->object_class;

    return new $class_name($this->pk_fields, $this->field_definitions);
  }

  public function getFieldDefinitions()
  {
    return $this->field_definitions;
  }

  public function __construct()
  {
    $this->initialize();

    if (is_null($this->connection))
    {
      throw new PgLookException(sprintf('PDO connection not set after initializing db map "%s".', get_class($this)));
    }
    if (is_null($this->object_class))
    {
      throw new PgLookException(sprintf('Missing object_class after initializing db map "%s".', get_class($this)));
    }
    if (count($this->field_definitions) == 0)
    {
      throw new PgLookException(sprintf('No fields after initializing db map "%s", don\'t you prefer anonymous objects ?', get_class($this)));
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
      if (!$stmt->execute())
      {
        throw new PgLookSqlException($stmt, $sql);
      }

      return $this->createObjectsFromStmt($stmt);
    }
    catch(PDOException $e)
    {
      throw new PgLookException('PDOException while performing SQL query «%s». The driver said "%s".', $sql, $e->getMessage());
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
      $object->hydrate($this->convertPg($values, 'fromPg'));

      $objects[] = $object;
    }

    return new PgLookCollection($objects);
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
      throw new PgLookException(sprintf('Given values "%s" do not match PK definition "%s" using class "%s".', print_r($values, true), print_r($this->getPrimaryKey(), true), get_class($this)));
    }

    $result = $this->findWhere($this->createSqlAndFrom($values), array_values($values));

    return count($result) == 1 ? $result[0] : null;
  }

  protected function convertPg(Array $values, $method)
  {
    $out_values = array();
    foreach ($values as $name => $value)
    {
      $converter = array_key_exists($name, $this->field_definitions) ? $this->field_definitions[$name] : null;
      if (is_null($converter))
      {
        $out_values[$name] = $value;
        continue;
      }
      if (is_null($value)) continue;

      if (!preg_match('/([a-z]+)(?:\[([a-z]+)\])?/i', $converter, $matchs))
      {
        throw new PgLookException(sprintf('Error, bad type converter expression "%s".', $converter));
      }
      $type = $matchs[1];
      $subtype = count($matchs) > 2 ? $matchs[2] : '';

      if ($subtype !== '')
      {
        call_user_func(array($type, 'setSubType'), $subtype);
      }

      $out_values[$name] = call_user_func(array($type, $method), $value);
    }

    return $out_values;
  }

  protected function checkObject(PgLookBaseObject $object, $message)
  {
    if (get_class($object) !== $this->object_class)
    {
      throw new PgLookException($message);
    }
  }

  public function deleteByPk(Array $pk)
  {
    $sql = sprintf('DELETE FROM %s WHERE %s', $this->object_name, $this->createSqlAndFrom($pk));
    return $this->query($sql, array_values($pk));
  }

  public function saveOne(PgLookBaseObject $object)
  {
    $this->checkObject($object, sprintf('"%s" class does not know how to save "%s" objects.', get_class($this), get_class($object)));

    if ($object->getStatus() & PgLookBaseObject::EXIST)
    {
      $sql = sprintf('UPDATE %s SET %s WHERE %s', $this->object_name, $this->parseForUpdate($object), $this->createSqlAndFrom($object->getPrimaryKey()));

      return $this->query($sql, array_values($object->getPrimaryKey()));
    }
    else
    {
      $pg_values = $this->parseForInsert($object);
      $sql = sprintf('INSERT INTO %s (%s) VALUES (%s) RETURNING *;', $this->object_name, join(',', array_keys($pg_values)), join(',', array_values($pg_values)));

      return $this->query($sql, array());
    }
  }

  protected function parseForInsert($object)
  {
    $tmp = array();
    foreach ($this->convertPg($object->getFields(), 'toPg') as $field_name => $field_value)
    {
      if (array_key_exists($field_name, $object->getPrimaryKey())) continue;
      $tmp[$field_name] = $field_value;
    }

    return $tmp;
  }

  protected function parseForUpdate($object)
  {
    $tmp = array();
    foreach ($this->convertPg($object->getFields(), 'toPg') as $field_name => $field_value)
    {
      if (array_key_exists($field_name, $object->getPrimaryKey())) continue;
      $tmp[] = sprintf('%s=%s', $field_name, $field_value);
    }

    return implode(',', $tmp);
  }
}
