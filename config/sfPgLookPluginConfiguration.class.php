<?php

class sfPgLookPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('context.load_factories', array('PgLook', 'setConnectionsEvent'));
    $this->dispatcher->connect('debug.web.load_panels', array($this, 'configureWebDebugToolbar'));
  }

  public static function configureWebDebugToolbar(sfEvent $event)
  {
    $web_debug = $event->getSubject();
    $web_debug->addPanel('pglook', new PgLookWebDebugPanel($web_debug));
  }
}
