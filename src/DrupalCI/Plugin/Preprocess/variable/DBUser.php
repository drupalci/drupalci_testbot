<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBUser.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

/**
 * @PluginID("dbuser")
 */
class DBUser extends DBUrlBase {

  public function process($dci_variable, $value) {
    return $this->buildUrl($dci_variable, 'user', $value);
  }

}
