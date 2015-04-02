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
    $dbtype = explode('-', $value, 2)[0];
    $host = 'drupaltestbot-db-' . str_replace('.', '-', $value);
    $dci_variable = $this->buildUrl($dci_variable, 'scheme', $dbtype);
    $dci_variable = $this->buildUrl($dci_variable, 'host', $host);
    return $dci_variable;
  }

}
