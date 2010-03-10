<?php
abstract class PlookBaseTableMap extends PlookBaseObjectMap 
{
  protected $pk_fields = array();

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

    $sql = $this->createSqlAndFrom($values);
    $stmt = $this->query(sprintf('SELECT * FROM %s WHERE %s', $this->getObjectName(), $sql), array_values($values));

    return $stmt->fetch($stmt);
  }

}
