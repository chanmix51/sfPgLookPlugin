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

  public function createTable()
  {
    $this->doQuery('CREATE TEMP TABLE test_table (id SERIAL PRIMARY KEY, created_at TIMESTAMP NOT NULL DEFAULT now(), title VARCHAR NOT NULL, authors VARCHAR[] NOT NULL, is_ok BOOLEAN NOT NULL DEFAULT true);',array());
  }
}

