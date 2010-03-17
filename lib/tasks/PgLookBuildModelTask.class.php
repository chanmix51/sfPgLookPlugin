<?php

class PgLookBuildModelTask extends sfBaseTask
{
  protected $schema;

  public function configure()
  {
    $this->namespace = 'pglook';
    $this->name      = 'build-model';
    $this->briefDescription = 'Generate PgLook model files based upon the schema definition.';
    $this->detailedDescription = <<<EOF
The [PgLook:build-model|INFO] generates your model class files based on the definition given in the config/pglook/schema.yml

Call it with:

  [php symfony pglook:build-model|INFO]
EOF;
  }
}
