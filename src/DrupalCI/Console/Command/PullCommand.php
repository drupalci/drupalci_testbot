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
    $images = $input->getArgument('container_name');
    // TODO: Validate passed arguments
    foreach ($images as $image) {
        $name = explode (':',$image);
        $container = $name[0];
        $tag = $name[1];
        if(empty($tag)) {
           $tag = 'latest';
        }
        Output::writeln("<comment>Pulling <options=bold>$container</options=bold> container</comment>");
        $this->pull($container ,$tag , $input);
    }
  }

  /**
   * (#inheritdoc)
   */
  protected function pull($name, $tag, InputInterface $input) {
    Output::writeln("-------------------- Start pulling --------------------");
    $manager = $this->getManager();
    $response = $manager->pull($name, $tag, function ($output) {
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
