<?php
abstract class PlookBaseTableMap extends PlookBaseObjectMap 
{
  protected function checkObject(PlookBaseRecordObject $object, $message)
  {
    if (get_class($object) !== $this->object_class)
    {
      throw new PlookException($message);
    }
  }

  public function deleteByPk(Array $pk)
  {
    $sql = sprintf('DELETE FROM %s WHERE %s', $this->object_name, $this->createSqlAndFrom($pk));
    return $this->query($sql, array_values($pk));
  }

  public function saveOne(PlookBaseRecordObject $object)
  {
    $this->checkObject($object, sprintf('"%s" class does not know how to save "%s" objects.', get_class($this), get_class($object)));

    if ($object->getStatus() & PlookBaseRecordObject::EXIST)
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

  protected function parseForUpdate($values)
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
