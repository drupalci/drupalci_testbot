<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\Concurrency.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;

/**
 * @PluginID("concurrency")
 */
class Concurrency extends PluginBase {

  public function target() {
    return 'DCI_RunScript';
  }

  public function process($dci_variable, $value) {
    return "$dci_variable --concurrency $value";
  }

}
