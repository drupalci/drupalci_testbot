<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\JobBuildId.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

/**
 * @PluginID("jobbuildid")
 */
class JobBuildId extends DBUrlBase {

  public function process($dci_variable, $value) {
    return $this->buildUrl($dci_variable, 'path', $value);
  }

}
