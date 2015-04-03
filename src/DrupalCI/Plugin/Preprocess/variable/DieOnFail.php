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

  /**
   * {@inheritdoc}
   */
  public function target() {
    return 'DCI_RunScript';
  }

  /**
   * {@inheritdoc}
   */
  public function process($run_script, $source_value) {
    if (strtolower($source_value) === 'true') {
      $run_script .=  ' --die-on-fail';
    }
    return $run_script;
  }

}
