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
use Symfony\Component\Console\Helper\ProgressBar;


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
        // check if we have a tag in the input
        if(!empty($name[1])) {
          $tag = $name[1];
        }
        else
        {
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
    $manager = $this->getManager();
    $progressInformation = array();
    $response = $manager->pull($name, $tag, function ($output) use (&$progressInformation) {

      // Initialize the Counting on how far we are away from completing the docker pull process
      $current_transfer = 0;
      $total_transfer = 0;
      foreach($progressInformation as $value ) {
        $current_transfer = $current_transfer + $value['current'];
        $total_transfer = $total_transfer + $value['total'];
      }

      // Add the progress data to the array we store in the closure
      if (isset($output['progressDetail']['total'])) {
        $progressInformation[$output['id']]['current'] = $output['progressDetail']['current'];
        $progressInformation[$output['id']]['total'] = $output['progressDetail']['total'];
        $progressInformation[$output['id']]['status'] = $output['status'];
        $progressInformation[$output['id']]['id'] = $output['id'];
      }

      // Start the progress bar and advance it all the time we run the output function
      $progressbar = new ProgressBar(Output::getOutput(), $total_transfer);
      $progressbar->start();
      $progressbar->advance($current_transfer);

      // foreach($progressInformation as $status){
      //   if(isset($status['status']) && isset($status['id'])){
      //     Output::write("<comment>".$status['id']." - ".$status['status']."</comment>");
      //     Output::write("<comment>".$status['id']."</comment>");
      //   }
      // }
    });
    // $response->getBody()->getContents();
    // Output::writeln((string) $response);
    Output::writeln("");
  }
}
