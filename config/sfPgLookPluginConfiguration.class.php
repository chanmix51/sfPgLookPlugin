<?php

class sfPgLookPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('context.load_factories', array('PgLook', 'setConnectionsEvent'));
  }
}
