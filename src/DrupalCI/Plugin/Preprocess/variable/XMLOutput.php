<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\XMLOutput.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;

/**
 * @PluginID("xmloutput")
 */
class XMLOutput extends PluginBase {

  public function target() {
    return 'DCI_RunScript';
  }

  public function process($dci_variable, $value) {
    return "$dci_variable --xml $value";
  }

}
