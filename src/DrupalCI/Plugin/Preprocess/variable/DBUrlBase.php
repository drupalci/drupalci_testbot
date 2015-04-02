<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBUser.
 */


namespace DrupalCI\Plugin\Preprocess\variable;
use DrupalCI\Plugin\PluginBase;

abstract class DBUrlBase extends PluginBase {

  public function key() {
    return 'DCI_DBURL';
  }

  protected function buildUrl($dci_variable, $key, $value) {
    $parts = parse_url($dci_variable);
    $parts[$key] = $value;
    if (isset($parts['pass']) && !isset($parts['user'])) {
      $parts['user'] = 'user';
    }
    $url = $parts['scheme'] . '://';
    if (isset($parts['user'])) {
      $url .= $parts['user'];
      if (isset($parts['pass'])) {
        $url .= ':' . $parts['pass'];
      }
      $url .= '@';
    }
    $url .= $parts['host'];
    return $url;
  }

}
