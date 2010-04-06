<?php

class pglookLoadsqlfileTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_OPTIONAL, 'The connection name'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED, 'The directory where to load files from', sfConfig::get('sf_data_dir').'/sql'),
    ));

    $this->namespace        = 'pglook';
    $this->name             = 'load-sql-files';
    $this->briefDescription = 'Load SQL files in the database';
    $this->detailedDescription = <<<EOF
The [pglook:load-sql-file|INFO] task loads one or several SQL files in the database. By default, all .sql files in the data/sql directory are loaded. If a directory is given with the [--dir|INFO] option, this directory will be used instead.

  [php symfony pglook:load-sql-file --dir=plugins/myPlugin/data/sql|INFO]

By default, PgLook will use the first database declared in the appropriate environnement in the [databases.yml|INFO] config file. This can be overriden with the [--connection|INFO] option.

  [php symfony pglook:load-sql-file --connection=my_connection|INFO]

If multiple files are present in the given directory, they will be loaded in alphabetical order.

example:

  [php symfony pglook:load-sql-file --dir=/usr/pgsql/data/sql --connection=pg1 --env=prod|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    PgLook::saveConnections(new sfDatabaseManager($this->configuration));
    $connection = PgLook::getConnection($options['connection']);

    // add your code here
  }
}
