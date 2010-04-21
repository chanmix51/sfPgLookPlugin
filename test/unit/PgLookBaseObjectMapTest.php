<?php

include(dirname(__FILE__).'/../../../../test/bootstrap/unit.php');

PgLook::saveConnections(new sfDatabaseManager($configuration));

class TestTable extends PgLookBaseObject
{
}

class TestTableMap extends PgLookBaseObjectMap
{
  protected function initialize()
  {
    $this->connection = PgLook::getConnection();
    $this->object_class = 'TestTable';
    $this->object_name = 'test_table';
    $this->field_definitions = array(
      'id' => 'PgLookIntType',
      'created_at' =>    'PgLookTimeStampType',
      'title'      =>    'PgLookStrType',
      'authors'    =>    'PgLookArrayType[PgLookStrType]',
      'is_ok'      =>    'PgLookBoolType'
    );
    $this->pk_fields    = array('id');
  }
}

class my_test 
{ 
  protected $test;
  protected $map;
  protected $obj;

  public function __construct()
  {
    $this->test = new lime_test();
    PgLook::executeAnonymousQuery('CREATE TEMP TABLE test_table (id SERIAL PRIMARY KEY, created_at TIMESTAMP NOT NULL DEFAULT now(), title VARCHAR NOT NULL, authors VARCHAR[] NOT NULL, is_ok BOOLEAN NOT NULL DEFAULT true);');
  }

  public function resetObjects()
  {
    $this->map = null;
    $this->obj = null;

    return $this;
  }

  public function testCreate()
  {
    $this->test->diag('TestTableMap::createObject()');
    $this->map = PgLook::getMapFor('TestTable');
    $this->obj = $this->map->createObject();

    $this->test->isa_ok($this->obj, 'TestTable', 'TestTableMap::createObject() returns a TestTable instance');
    $this->test->is($this->obj->_getStatus(), PgLookBaseObject::NONE, 'Object does not exist nor is modified');

    return $this;
  }

  protected function testObjectFields($values)
  {
    foreach ($this->obj->getFields() as $name => $value)
    {
      if (gettype($value) == 'object') continue;
      $this->test->is($value, $values[$name], sprintf('Comparing "%s"', $name));
    }

    return $this;
  }

  public function testHydrate($values, $tested_values)
  {
    $this->test->diag('TestTableMap::hydrate()');
    $this->obj->hydrate($values);
    $this->testObjectFields($tested_values);

    return $this;
  }

  public function testSaveOne()
  {
    $this->test->diag('TestTableMap::saveOne()');
    try
    {
      $this->map->saveOne($this->obj);
      $this->test->is($this->obj->_getStatus(), PgLookBaseObject::EXIST, 'Object does exist but is NOT modified');
      $this->test->ok($this->obj->getId(), 'Object has an ID');
    }
    catch(PgLookException $e)
    {
      $this->test->fail('Error while saving object');
      $this->test->skip(1);
    }

    return $this;
  }

  public function testRetreiveByPk($values)
  {
    $this->test->diag('TestTableMap::findByPk()');
    $object = $this->map->findByPk($values);
    $this->testObjectFields($object->getFields());

    return $this;
  }

  public function testDeleteOne()
  {
    $this->test->diag('TestTableMap::deleteOne()');
    try
    {
      $this->map->deleteOne($this->obj);
      $this->test->pass('No error during deletion');
      $this->test->is($this->obj->_getStatus(), PgLookBaseObject::NONE, 'status = NONE');
      $this->test->ok(is_null($this->map->findByPk(array('id' => $this->obj->getId()))), 'Record is not in the database anymore');
    }
    catch (PgLookException $e)
    {
      $this->test->fail('Deletion error');
    }

    return $this;
  }
}

$test = new my_test();
$test->testCreate()
  ->testHydrate(array('title' => 'title test', 'authors' => array('pika chu')), array('title' => 'title test', 'authors' => array('pika chu')))
  ->testSaveOne()
  ->testHydrate(array(), array('id' => 1, 'title' => 'title test', 'authors' => array('pika chu'), 'is_ok' => true))
  ->testRetreiveByPk(array('id' => 1))
  ->testHydrate(array('title' => 'modified title', 'authors' => array('pika chu', 'john doe')), array('id' => 1, 'title' => 'modified title', 'authors' => array('pika chu', 'john doe'), 'is_ok' => true))
  ->testSaveOne()
  ->testHydrate(array(), array('id' => 1, 'title' => 'modified title', 'authors' => array('pika chu', 'john doe'), 'is_ok' => true))
  ->testRetreiveByPk(array('id' => 1))
  ->testDeleteOne()
  ;
