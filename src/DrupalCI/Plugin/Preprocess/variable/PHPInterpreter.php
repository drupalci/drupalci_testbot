<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\SQLite.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;

/**
 * @PluginID("phpinterpreter")
 */
class PHPInterpreter extends PluginBase {

  /**
   * {@inheritdoc}
   */
  public function target() {
    return 'DCI_RunScript';
  }

  /**
   * {@inheritdoc}
   */
  public function process($run_script, $php_path) {
    return "$run_script --php $php_path";
  }

}
