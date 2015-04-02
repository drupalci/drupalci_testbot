<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\setup\CheckoutTest.
 */


namespace DrupalCI\Tests\Plugin\BuildSteps\setup;


use DrupalCI\Plugin\BuildSteps\setup\Checkout;
use DrupalCI\Tests\DrupalCITestCase;

class CheckoutTest extends DrupalCITestCase {

  public function testRunGit() {
    $dir = 'test/dir';
    $data = [
      'protocol' => 'git',
      'repo' => 'git://code.drupal.org/drupal.git',
      'branch' => '8.0.x',
      'checkout_dir' => $dir,
      'depth' => 1,
    ];
    $checkout = new TestCheckout([], 'checkout', []);
    $checkout->setValidate($dir);
    $checkout->run($this->job, $data);
    $this->assertSame(['git clone -b 8.0.x git://code.drupal.org/drupal.git test/dir --depth=1'], $checkout->getCommands());
  }
}

class TestCheckout extends Checkout {
  use TestSetupBaseTrait;
}
