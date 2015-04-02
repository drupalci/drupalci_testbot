<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\setup\TestSetupBase.
 */

namespace DrupalCI\Tests\Plugin\BuildSteps\setup;

use DrupalCI\Plugin\JobTypes\JobInterface;

trait TestSetupValidateDirTrait {

  protected $validate;

  function validateDirectory(JobInterface $job, $dir) {
    return $this->validate;
  }

  function setValidate($validate) {
    $this->validate = $validate;
  }


}
