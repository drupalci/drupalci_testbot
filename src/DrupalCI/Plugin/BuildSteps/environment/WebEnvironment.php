<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\environment\WebEnvironment
 *
 * Processes "environment: web:" parameters from within a job definition,
 * ensures appropriate Docker container images exist, and defines the
 * appropriate execution container for communication back to JobBase.
 */

namespace DrupalCI\Plugin\BuildSteps\environment;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("web")
 */
class WebEnvironment extends PhpEnvironment {

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format: '5.5' or array('5.4', '5.5')
    // $data May be a string if one version required, or array if multiple
    // Normalize data to the array format, if necessary
    $data = is_array($data) ? $data : [$data];
    Output::writeLn("<comment>Parsing required container image names ...</comment>");
    $containers = $job->getExecContainers();
    $containers['web'] = $this->buildImageNames($data, $job);
    $valid = $this->validateImageNames($containers['web'], $job);
    if (!empty($valid)) {
      $job->setExecContainers($containers);
      // Actual creation and configuration of the executable containers will occur in the 'execute' plugin.
    }
  }

  protected function buildImageNames($data, JobInterface $job) {
    $images = [];
    foreach ($data as $key => $web_version) {
      $images["$web_version"]['image'] = "drupalci/$web_version";
      Output::writeLn("<info>Adding image: <options=bold>drupalci/$web_version</options=bold></info>");
    }
    return $images;
  }

}
