<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\setup\PatchTest.
 */


namespace DrupalCI\Tests\Plugin\BuildSteps\setup;

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
    $patch = new TestPatch();
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
  use TestSetupBaseTrait;
}
