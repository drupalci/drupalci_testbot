<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DieOnFail.
 */

namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;

/**
 * @PluginID("dieonfail")
 */
class DieOnFail extends PluginBase {

  public function key() {
    return 'DCI_RunScript';
  }

  public function process($dci_variable, $value) {
    if (strtolower($value) === 'true') {
      $dci_variable .=  ' --die-on-fail';
    }
    return $dci_variable;
  }

}
