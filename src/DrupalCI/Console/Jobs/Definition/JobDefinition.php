<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 1/19/15
 * Time: 7:50 PM
 */

namespace DrupalCI\Console\Jobs\Definition;

use Symfony\Component\Yaml\Parser;

class JobDefinition {

  // The definition source may be a local file or URL
  protected $source = NULL;

  public function setSource($filename) {
    $this->source = $filename;
  }

  public function getSource() {
    return $this->source;
  }

  // Placeholder for parsed key=>value parameter pairs
  protected $parameters = array();

  public function load() {
    $source = $this->source;
    if (empty($source)) {
      // TODO: Throw exception
      return;
    }
    $yaml = new Parser();
    if ($content = file_get_contents($source)) {
      $parameters = $yaml->parse($content);
    }
    else {
      // TODO: Error Handling
      return -1;
    }
    $this->parameters = $parameters;
    return;
  }

  public function getParameters() {
    return $this->parameters;
  }

  public function setParameters(array $parameters) {
    $this->parameters = $parameters;
  }

}