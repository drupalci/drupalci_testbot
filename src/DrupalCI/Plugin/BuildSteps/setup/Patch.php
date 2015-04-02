<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\setup\Patch
 *
 * Processes "setup: patch:" instructions from within a job definition.
 */

namespace DrupalCI\Plugin\BuildSteps\setup;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("patch")
 */
class Patch extends SetupBase {

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format:
    // i) array('patch_file' => '...', 'patch_dir' => '...')
    // or
    // iii) array(array(...), array(...))
    // Normalize data to the third format, if necessary
    $data = (count($data) == count($data, COUNT_RECURSIVE)) ? [$data] : $data;
    Output::writeLn("<info>Entering setup_patch().</info>");
    foreach ($data as $key => $details) {
      if (empty($details['patch_file'])) {
        $job->errorOutput("Error", "No valid patch file provided for the patch command.");
        return;
      }
      $workingdir = realpath($job->getWorkingDir());
      $patchfile = $details['patch_file'];
      $patchdir = (!empty($details['patch_dir'])) ? $details['patch_dir'] : $workingdir;
      // Validate target directory.
      if (!($directory = $this->validateDirectory($job, $patchdir))) {
        // Invalid checkout directory
        $job->errorOutput("Error", "The patch directory <info>$directory</info> is invalid.");
        return;
      }
      $cmd = "patch -p1 -i $patchfile -d $directory";

      $this->exec($cmd, $cmdoutput, $result);
      if ($result !== 0) {
        // The command threw an error.
        $job->errorOutput("Patch failed", "The patch attempt returned an error.");
        Output::writeLn($cmdoutput);
        // TODO: Pass on the actual return value for the patch attempt
        return;
      }
      Output::writeLn("<comment>Patch <options=bold>$patchfile</options=bold> applied to directory <options=bold>$directory</options=bold></comment>");
    }
  }

  protected function exec($command, &$output, &$return_var) {
    exec($command, $output, $return_var);
  }
}
