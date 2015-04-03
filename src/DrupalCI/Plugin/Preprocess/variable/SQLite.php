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

  public function key() {
    return 'DCI_RunScript';
  }

  public function process($dci_variable, $value) {
    return "$dci_variable --sqlite $value";
  }

}
