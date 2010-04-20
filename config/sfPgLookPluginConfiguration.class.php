<?php

class sfPgLookPluginConfiguration extends sfPluginConfiguration
{
  /**
   * initialize 
   * Catch when the factories are loaded to grab the sfPgLookDatabases
   * 
   * @access public
   * @return void
   */
  public function initialize()
  {
    $this->dispatcher->connect('context.load_factories', array('PgLook', 'setConnectionsEvent'));
  }
}
