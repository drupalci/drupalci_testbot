<?php
/**
 * @file
 * Contains
 */
namespace DrupalCI\Plugin\JobTypes;

use Symfony\Component\Console\Output\OutputInterface;

interface JobInterface {

  /**
   * An array of build variables.
   *
   * - DCI_DEPENDENCIES
   * - DCI_DEPENDENCIES_GIT
   * - DCI_TravisFile
   * - DCI_Privileged
   *
   * @return array
   *
   * @see SimpletestJob::$availableArguments
   */
  public function getBuildVars();

  /**
   * @param array $build_vars
   *
   * @see JobInterface::getBuildvards
   */
  public function setBuildVars(array $build_vars);

  /**
   * @param string $build_var
   *
   * @return mixed
   *
   * @see JobInterface::getBuildvards
   */
  public function getBuildvar($build_var);

  /**
   * @param $build_var
   * @param $value
   */
  public function setBuildVar($build_var, $value);

  /**
   * Required arguments.
   *
   * @TODO: move to annotation
   *
   * @return array
   *
   * @see SimpletestJob::$requiredArguments
   */
  public function getRequiredArguments();

  /**
   * @return \Symfony\Component\Console\Output\OutputInterface
   */
  public function getOutput();

  /**
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   */
  public function setOutput(OutputInterface $output);

  /**
   * Sends an error message.
   *
   * @param string $type
   * @param string $message
   * @return mixed
   */
  public function errorOutput($type = 'Error', $message = 'DrupalCI has encountered an error.');

  /**
   * Execute a shell command.
   *
   * @param string $cmd
   *   The commmand line.
   *
   * @see \Symfony\Component\Process\Process::__construct().
   */
  public function shellCommand($cmd);

  /**
   * @return \Docker\Docker
   */
  public function getDocker();

  /**
   * Get a list of containers to run Docker exec in.
   *
   * @return array
   *  An array of container IDs. The first key is the type, can be 'php' or
   *  'web'. Web has everything php plus Apache.
   */
  public function getExecContainers();

  public function setExecContainers(array $containers);

  public function startContainer(&$container);

  public function getContainerConfiguration($image = NULL);

  public function startServiceContainerDaemons($type);

  public function getErrorState();

  public function getDefinition();

  public function setDefinition(array $job_definition);

  public function getDefaultArguments();

  public function getPlatformDefaults();

  public function getServiceContainers();

  public function setServiceContainers(array $service_containers);

  public function getWorkingDir();

  public function setWorkingDir($working_directory);
}
