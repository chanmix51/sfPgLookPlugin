<?php

class PlookBuildModelTask extends sfBaseTask
{
  protected $schema;

  public function configure()
  {
    $this->namespace = 'plook';
    $this->name      = 'build-model';
    $this->briefDescription = 'Generate Plook model files based upon the schema definition.';
    $this->detailedDescription = <<<EOF
The [Plook:build-model|INFO] generates your model class files based on the definition given in the config/plook/schema.yml

Call it with:

  [php symfony plook:build-model|INFO]
EOF;
  }
}
