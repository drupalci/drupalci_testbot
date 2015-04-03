<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\variable\DBUser.
 */


namespace DrupalCI\Plugin\Preprocess\variable;

use DrupalCI\Plugin\PluginBase;
use DrupalCI\Plugin\Preprocess\VariableInterface;

abstract class DBUrlBase extends PluginBase implements VariableInterface {

  /**
   * {@inheritdoc}
   */
  public function target() {
    return 'DCI_DBURL';
  }

  /**
   * Change one part of the URL.
   *
   * @param $db_url
   *   The value of the DCI_DBURL variable.
   * @param $part
   *   The URL part being replaced. Can be scheme, user, pass, host or path.
   * @param $value
   *   The new value of the URL part.
   * @return string
   *   The new DCI_DBURL.
   */
  protected function changeUrlPart($db_url, $part, $value) {
    $parts = parse_url($db_url);
    $parts[$part] = $value;
    if (isset($parts['pass']) && !isset($parts['user'])) {
      $parts['user'] = 'user';
    }
    $new_url = $parts['scheme'] . '://';
    if (isset($parts['user'])) {
      $new_url .= $parts['user'];
      if (isset($parts['pass'])) {
        $new_url .= ':' . $parts['pass'];
      }
      $new_url .= '@';
    }
    $new_url .= $parts['host'];
    if (isset($parts['path'])) {
      $new_url .= $parts['path'];
    }
    return $new_url;
  }

}
