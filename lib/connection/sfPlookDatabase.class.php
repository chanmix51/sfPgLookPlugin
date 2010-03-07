<?php

class sfPlookDatabase extends sfDatabase
{
  protected $_handler;

  public function __construct($parameters = array())
  {
    parent::initialize($parameters);

    if (null !== $this->_handler)
    {
      return;
    }

    $this->processDsn();

    if (!$this->hasParameter('persistant'))
    {
      $this->setParameter('persistant', false);
    }
  }

  protected function processDsn()
  {
    $dsn = $this->getParameter('dsn');

    if (!preg_match('#([a-z]+):(?://(\w+)(:\w+)?@(\w+)(:\w+)?)?/(\w+)#', $dsn, $matchs))
    {
      throw sfConfigurationException(sprintf('Cound not parse DSN "%s".', $dsn));
    }

    if ($adapter = $matchs[1] == null)
    {
      throw sfConfigurationException(sprintf('No protocol information in dsn "%s".', $dsn));
    }

    if ($user = $matchs[2] == null)
    {
      throw sfConfigurationException(sprintf('No user information in dsn "%s".', $dsn));
    }

    $pass = $matchs[3];

    if ($host = $matchs[4] == null)
    {
      throw sfConfigurationException(sprintf('No hostname name in dsn "%s".', $dsn));
    }

    $port = $matchs[5];

    if ($database = $matchs[6] == null)
    {
      throw sfConfigurationException(sprintf('No database name in dsn "%s".', $dsn));
    }

    $this->setParameter('adapter', $adapter);
    $this->setParameter('user', $user);
    $this->setParameter('pass', $pass);
    $this->setParameter('host', $host);
    $this->setParameter('port', $port);
    $this->setParameter('database', $database);
  }

  public function connect()
  {
    $connect_string = sprintf('%s:host=%s dbname=%s user=%s', 
        $this->getParameter('host'),
        $this->getParameter('database'),
        $this->getParameter('user') 
        );

    $connect_string .= $this->getParameter('port') !== '' ? sprintf(' port=%d', $this->getParameter('port')) : '';
    $connect_string .= $this->getParameter('pass') !== '' ? sprintf(' password=%d', $this->getParameter('pass')) : '';

    try
    {
      $this->_handler = new PDO($connect_string);
    }
    catch (PDOException $e)
    {
      throw new PlookException('Error connecting to the database. Driver said "%s".', $e->getMessage());
    }
  }

  public function shutdown()
  {
    $this->_handler = null;
  }
}
