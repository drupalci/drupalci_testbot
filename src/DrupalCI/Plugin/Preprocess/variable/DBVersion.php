<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBVersion.
 */

namespace DrupalCI\Plugin\Preprocess\variable;

/**
 * @PluginID("dbversion")
 */
class DBVersion extends DBUrlBase {

  public function process($dci_variable, $value) {
    return $this->buildUrl($dci_variable, 'scheme', explode('-', $value, 2)[0]);
  }

}
