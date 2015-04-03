<?php

/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\generic\CreateDirectory.
 */

namespace DrupalCI\Plugin\BuildSteps\generic;

use DrupalCI\Plugin\BuildSteps\generic\ContainerCommand;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("createdirectory")
 */
class CreateDirectory extends ContainerCommand {

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format: 'directory' or array('directory1', 'directory2')
    // $data May be a string if one directory required, or array if multiple
    // Normalize data to the array format, if necessary
    $data = is_array($data) ? $data : [$data];
    foreach ($data as $directory) {
      $cmd = "mkdir -p $directory";
    }
    parent::run($job, $cmd);
  }
}