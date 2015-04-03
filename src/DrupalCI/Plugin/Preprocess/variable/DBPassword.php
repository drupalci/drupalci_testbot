<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBPass.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

/**
 * @PluginID("dbpassword")
 */
class DBPassword extends DBUrlBase {

  /**
   * {@inheritdoc}
   */
  public function process($db_url, $source_value) {
    return $this->changeUrlPart($db_url, 'pass', $source_value);
  }

}
