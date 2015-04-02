<?php

/**
 * @file
 * Command class for run.
 */

namespace DrupalCI\Console\Command;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\PluginManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends DrupalCICommandBase {

  /**
   * @var \DrupalCI\Plugin\PluginManagerInterface
   */
  protected $buildStepsPluginManager;

  /**
   * @var \DrupalCI\Plugin\PluginManagerInterface
   */
  protected $jobPluginManager;

  /**
   * {@inheritdoc}
   *
   * Options:
   *   Will probably be a combination of things taken from environment variables
   *   and job specific options.
   *   TODO: Sort out how to define job-specific options, and be able to import
   *   them into the drupalci command. (Imported from a specially named file in
   *   the job directory, perhaps?) Will need syntax to define required versus
   *   optional options, and their defaults if not specified.
   */
  protected function configure() {
    $this
      ->setName('run')
      ->setDescription('Execute a given job run.')
      ->addArgument('definition', InputArgument::OPTIONAL, 'Job definition.');
    // TODO: Add 'definition file name' as an option
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $definition = $input->getArgument('definition');
    // The definition argument is optional, so we need to set a default definition file if it's not provided.
    if (!$definition) {
      $definition = "./drupalci.yml";
    }
    // Populate the job type and definition file variables
    if (substr(trim($definition), -4) == ".yml") {
      // "File" arguments
      $job_type = 'generic';
      $definition_file = $definition;
    }
    else {
      // "Job Type" arguments
      $job_type = $definition;
      $definition_file = __DIR__ . "/../../Plugin/JobTypes/$job_type/drupalci.yml";
    }
    // TODO: Make sure $definition_file exists

    /** @var $job \DrupalCI\Plugin\JobTypes\JobInterface */
    $job = $this->jobPluginManager()->getPlugin($job_type, $job_type);

    // Link our $output variable to the job, so that jobs can display their work.
    Output::setOutput($output);

    // Store the definition file argument in the job so we can act on it later
    $job->setDefinitionFile($definition_file);

    // Create a unique job build_id
    $build_id = $job_type . '_' . time();

    $job->setBuildId($build_id);

    // Load the job definition, environment defaults, and any job-specific configuration steps which need to occur
    foreach (['compile_definition', 'validate_definition', 'setup_directories'] as $step) {
      $this->buildstepsPluginManager()->getPlugin('configure', $step)->run($job, NULL);
    }

    if ($job->getErrorState()) {
      $output->writeln("<error>Job halted due to an error while configuring job.</error>");
      return;
    }

    // The job should now have a fully merged job definition file, including
    // any local or drupalci defaults not otherwise defined in the passed job
    // definition, located in $job->job_definition
    $definition = $job->getDefinition();
    foreach ($definition as $build_step => $step) {
      foreach ($step as $plugin => $data) {
        $this->buildstepsPluginManager()->getPlugin($build_step, $plugin)->run($job, $data);
        if ($job->getErrorState()) {
          // Step returned an error.  Halt execution.
          // TODO: Graceful handling of early exit states.
          $output->writeln("<error>Job halted.</error>");
          $output->writeln("<comment>Exiting job due to an invalid return code during job build step: <options=bold>'$build_step=>$plugin'</options=bold></comment>");
          break 2;
        }
      }
    }
  }

  /**
   * @return \DrupalCI\Plugin\PluginManagerInterface
   */
  protected function buildstepsPluginManager() {
    if (!isset($this->buildStepsPluginManager)) {
      $this->buildStepsPluginManager = new PluginManager('BuildSteps');
    }
    return $this->buildStepsPluginManager;
  }

    /**
   * @return \DrupalCI\Plugin\PluginManagerInterface
   */
  protected function jobPluginManager() {
    if (!isset($this->jobPluginManager)) {
      $this->jobPluginManager = new PluginManager('JobTypes');
    }
    return $this->jobPluginManager;
  }

}
