<?php
abstract class PlookBaseTableMap extends PlookBaseObjectMap 
{
  protected function checkObject(PlookBaseRecordObject $object, $message)
  {
    if (get_class($object) !== $this->class_name)
    {
      throw new PlookException($message);
    }
  }

  public function deleteOne(PlookBaseRecordObject $object)
  {
    $this->checkObject($object, sprintf('"%s" class does not know how to delete "%s" objects.', get_class($this), get_class($objects)));

    if (!$object->getStatus() & PlookBaseObject::EXIST)
    {
      throw new PlookException(sprintf('Given object class "%s" is not marked as existing in the database (status %d).', get_class($object), $object->getStatus()));
    }

    $sql = sprintf('DELETE FROM %s WHERE %s', $this->object_name, $this->createSqlAndFrom(array_keys($object->getPrimaryKey())));
    $this->query($sql, array_values($object->getPrimaryKey()));
  }
/*
  public function saveOne(PlookBaseRecordObject $object)
  {
    $this->checkObject($object, sprintf('"%s" class does not know how to save "%s" objects.', get_class($this), get_class($objects)));

    if ($object->getStatus() && PlookBaseRecordObject::EXIST)
    {
      $sql = sprintf('UPDATE %s SET %s WHERE %s', $this->object_name, , $this->createSqlAndFrom($object->getPrimaryKey()));
*/

}
