<?php

/**
 * @file
 * Contains \DrupalCI\Tests\DrupalCITestCase.
 */

namespace DrupalCI\Tests;

use DrupalCI\Console\Output;

class DrupalCITestCase extends \PHPUnit_Framework_TestCase {

  /**
   * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $output;

  public function setUp() {
    $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
    Output::setOutput($this->output);
  }
}
