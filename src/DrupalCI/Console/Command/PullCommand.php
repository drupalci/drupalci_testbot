<?php

/**
 * @file
 * Command class for pull.
 */

namespace DrupalCI\Console\Command;

use DrupalCI\Console\Command\DrupalCICommandBase;
use DrupalCI\Console\Helpers\ContainerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Docker\Context\Context;
use DrupalCI\Console\Output;

class PullCommand extends DrupalCICommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('pull')
      ->setDescription('Pull DrupalCI container image from hub.docker.com.')
      ->addArgument('container_name', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Docker container image(s) to pull.')
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    Output::setOutput($output);
    $output->writeln("<info>Executing pull ...</info>");
    $names = $input->getArgument('container_name');
    // TODO: Validate passed arguments
    foreach ($names as $name) {
        Output::writeln("<comment>Pulling <options=bold>$name</options=bold> container</comment>");
        $this->pull($name, $input);
    }
  }

  /**
   * (#inheritdoc)
   */
  protected function pull($name, InputInterface $input) {
    Output::writeln("-------------------- Start pulling --------------------");
    $manager = $this->getManager();
    // $manager->pull('ubuntu');
    $response = $manager->pull($name, $tag = 'latest', function ($output) {
      if (isset($output['stream'])) {
        Output::writeLn('<info>' . $output['stream'] . '</info>');
      }
      elseif (isset($output['error'])) {
        Output::error('Error', $output['error']);
      }
    });
    Output::writeln("--------------------- End pulling ---------------------");
    $response->getBody()->getContents();
    Output::writeln((string) $response);
  }
}
