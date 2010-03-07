<?php

class sfPlookPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('context.load_factories', array('Plook', 'setConnectionsEvent'));
  }
}
