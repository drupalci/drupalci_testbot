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

  /**
   * {@inheritdoc}
   */
  public function process($db_url, $source_value) {
    $dbtype = explode('-', $source_value, 2)[0];
    $host = 'drupaltestbot-db-' . str_replace('.', '-', $source_value);
    $db_url = $this->changeUrlPart($db_url, 'scheme', $dbtype);
    $db_url = $this->changeUrlPart($db_url, 'host', $host);
    return $db_url;
  }

}
