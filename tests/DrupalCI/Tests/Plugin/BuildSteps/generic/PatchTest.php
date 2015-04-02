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

  /**
   * @dataProvider runProvider
   */
  public function testRun($error_count, $validate, $exec_result, $commands) {
    $this->job->expects($this->exactly($error_count))
      ->method('errorOutput');
    $patch = new TestPatch([], 'patch', []);
    $args = [
      '%dir' => 'patch/dir',
      '%file' => 'test.file',
    ];
    $patch->setValidate(strtr($validate, $args));
    $patch->setExecResult($exec_result);
    $data = [
      ['patch_file' => $args['%file'], 'patch_dir' => $args['%dir']],
    ];
    $patch->run($this->job, $data);
    if ($commands) {
      $commands = array_map(function ($command) use ($args) {
        return strtr($command, $args);
      }, $commands);
      $this->assertSame($commands, $patch->getCommands());
    }
  }

  public function runProvider() {
    return [
      [1, FALSE, 1, []],
      [1, '%dir', 1, ["patch -p1 -i %file -d %dir"]],
      [0, '%dir', 0, ["patch -p1 -i %file -d %dir"]],
    ];
  }
}

class TestPatch extends Patch {

  protected $commands = [];

  protected $validate = TRUE;

  protected $execResult;

  function exec($command, &$output, &$result) {
    $this->commands[] = $command;
    $result = $this->execResult;
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
