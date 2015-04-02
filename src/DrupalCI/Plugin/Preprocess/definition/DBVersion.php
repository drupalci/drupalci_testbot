<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\DBVersion.
 */

namespace DrupalCI\Plugin\Preprocess\definition;

/**
 * @PluginID("dbversion")
 */
class DBVersion {

  public function process(array &$definition, $value) {
    $length = array_search('install', array_keys($definition));
    $dbtype = explode('-', $value, 2)[0];
    $definition =
      array_slice($definition, 0, $length, TRUE) +
      ['dbinstall' => [$dbtype => TRUE]] +
      array_slice($definition, $length, NULL, TRUE);
  }
}
