<?php

class PgLookWebDebugPanel extends sfWebDebugPanel
{
  public function getTitle()
  {
    return 'pglook';
  }

  public function getPanelTitle()
  {
    return 'Get queries passed with PgLook.';
  }

  public function getPanelContent()
  {
  }
}

