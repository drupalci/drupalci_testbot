<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBPass.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

/**
 * @PluginID("dbpass")
 */
class DBPass extends DBUrlBase {

  public function process($dci_variable, $value) {
    return $this->buildUrl($dci_variable, 'pass', $value);
  }

}
