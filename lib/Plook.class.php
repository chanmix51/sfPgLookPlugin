<?php

class Plook
{
  static $connections = array();

  static function setConnectionsEvent(sfEvent $event)
  {
    $db_manager = $event->getSubject()->getDatabaseManager();
    foreach ($db_manager->getNames() as $name)
    {
      if ($db_manager->getDatabase($name) instanceof sfPlookDatabase)
      {
        self::$connections[$name] = $db_manager->getDatabase($name);
      }
    }
  }

  public static function getConnection($name)
  {
    if (array_key_exists($name, self::$connections))
    {
      return self::$connections[$name];
    }

    throw new PlookException(sprintf('No database connection with this name "%s".', $name));
  }
}
