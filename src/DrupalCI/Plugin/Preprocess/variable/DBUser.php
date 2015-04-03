<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBUser.
 */


namespace DrupalCI\Plugin\Preprocess\variable;
use DrupalCI\Plugin\Preprocess\VariableInterface;

/**
 * @PluginID("dbuser")
 */
class DBUser extends DBUrlBase implements VariableInterface {

  /**
   * {@inheritdoc}
   */
  public function process($db_url, $source_value) {
    return $this->changeUrlPart($db_url, 'user', $source_value);
  }

}
