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

  /**
   * {@inheritdoc}
   */
  public function process($db_url, $source_value) {
    return $this->changeUrlPart($db_url, 'path', "/$source_value");
  }

}
