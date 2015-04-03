<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\SQLite.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;

/**
 * @PluginID("sqlite")
 */
class SQLite extends PluginBase {

  /**
   * {@inheritdoc}
   */
  public function target() {
    return 'DCI_RunScript';
  }

  /**
   * {@inheritdoc}
   */
  public function process($run_script, $sqlite) {
    return "$run_script --sqlite $sqlite";
  }

}
