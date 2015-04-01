<?php

/**
 * @file
 * Command class for build.
 */

namespace DrupalCI\Console\Command;

use DrupalCI\Console\Helpers\ContainerHelper;
use DrupalCI\Console\Output;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Docker\Context\Context;

class BuildCommand extends DrupalCICommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('build')
      ->setDescription('Build DrupalCI container image.')
      ->addArgument('container_name', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Docker container image(s) to build.')
    ;
      #->addOption(
      #  'dbtype', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Database types to support', array('mysql')
      #)
      #->addOption('php_version', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'PHP Versions to support', array('5.4'))
      #->addOption('container_type', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Types of container image (db/web) to build.', array('web'))
      #->addOption('container_name', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Names of a specific container image to build.');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    Output::setOutput($output);
    $output->writeln("<info>Executing build ...</info>");
    $helper = new ContainerHelper();
    $containers = $helper->getAllContainers();
    $names = $input->getArgument('container_name');
    // TODO: Validate passed arguments
    foreach ($names as $name) {
      if (in_array($name, array_keys($containers))) {
        Output::writeln("<comment>Building <options=bold>$name</options=bold> container</comment>");
        $this->build($name, $input);
      }
      else {
        // Container name not found.  Skip build.
        Output::writeln("<error>No '$name' container found.  Skipping container build.</error>");
        // TODO: Error handling
      }
    }
  }

  /**
   * (#inheritdoc)
   */
  protected function build($name, InputInterface $input) {
    $helper = new ContainerHelper();
    $containers = $helper->getAllContainers();
    $container_path = $containers[$name];
    $docker = $this->getDocker();
    $context = new Context($container_path);
    Output::writeln("-------------------- Start build script --------------------");
    $response = $docker->build($context, $name, function ($output) use (&$content) {
      if (isset($output['stream'])) {
        $content .= $output['stream'];
      }
    });
    Output::writeLn("<info>$content</info>");

    //$response = $docker->build($context, $name, function ($result) use (&$content) {
    //  if (isset($result['stream'])) {
    //    $content .= $result['stream'];
    //  }
    //});
    //$output->writeln($content);
    Output::writeln("--------------------- End build script ---------------------");
    $response->getBody()->getContents();
    Output::writeln((string) $response);

    // TODO: Capture return value and determine whether build was successful or not, throwing an error if it isn't.
    // (This may already automatically throw an exception within docker-php)
    /* LEGACY CODE - original notification
    if ($return_var === 0) {
      $output->writeln("<comment>Container <options=bold>$name</options=bold> build complete.</comment>");
      $output->writeln("<comment>The $name container image should now be available.</comment>");
    }
    else {
      $output->writeln("<error>Build script exited with a non-zero error code: <options=bold>$return_var</options=bold></error>");
      $output->writeln("<comment>Please review the output above to determine the root cause.</comment>");
    }
    */
  }
}
