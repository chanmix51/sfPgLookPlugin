<?php

class PgLook
{
  const VERSION = 'ALPHA - 10e1Dev';
  static $connections = array();

  public static function saveConnections(sfDatabaseManager $db_manager)
  {
    foreach ($db_manager->getNames() as $name)
    {
      if ($db_manager->getDatabase($name) instanceof sfPgLookDatabase)
      {
        self::$connections[$name] = $db_manager->getDatabase($name);
      }
    }
  }

  public static function setConnectionsEvent(sfEvent $event)
  {
    self::saveConnections($event->getSubject()->getDatabaseManager());
  }

  public static function getConnection($name = null)
  {
    if (is_null($name))
    {
      if (count(self::$connections) == 0)
      {
        throw new PgLookException(sprintf('No database connections.'));
      }
      else
      {
        $cnx = array_values(self::$connections);
        return $cnx[0];
      }
    }
    if (array_key_exists($name, self::$connections))
    {
      return self::$connections[$name];
    }

    throw new PgLookException(sprintf('No database connection with this name "%s".', $name));
  }

  public static function executeAnonymousSelect($sql, $connection = null)
  {
    return self::getConnection($connection)->getPdo()->query($sql, PDO::GET_OBJECT);
  }

  public static function getMapFor($class)
  {
    $class_name = $class.'Map';

    return new $class_name();
  }
}
