<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\generic\PatchTest.
 */


namespace DrupalCI\Tests\Plugin\BuildSteps\generic;


use DrupalCI\Plugin\BuildSteps\setup\Patch;
use DrupalCI\Plugin\JobTypes\JobInterface;
use DrupalCI\Tests\DrupalCITestCase;

class PatchTest extends DrupalCITestCase {

  function testRunwithFailingValidation() {
    $patch = new TestPatch([], 'patch', []);
    $patch->setValidate(FALSE);
    $data = [
      ['patch_file' => 'test.file', 'patch_dir' => 'patch/dir'],
    ];
    $patch->run($this->job, $data);
    $this->assertEmpty($patch->getCommands());
  }



}

class TestPatch extends Patch {

  protected $commands = [];

  protected $validate = TRUE;

  protected $execResult;

  function exec($command, &$output, &$result) {
    $this->commands[] = $command;
    return $this->execResult;
  }

  function getCommands() {
    return $this->commands;
  }

  function validateDirectory(JobInterface $job, $dir) {
    return $this->validate;
  }

  function setValidate($validate) {
    $this->validate = $validate;
  }

  function setExecResult($exec_result) {
    $this->execResult = $exec_result;
  }
}
