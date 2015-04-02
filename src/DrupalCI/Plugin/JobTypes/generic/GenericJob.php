<?php

/**
 * @file
 * Job class for 'Generic' jobs on DrupalCI.
 *
 * A generic job simply runs through and executes the job definition steps as
 * defined within the passed job definition file.
 */

namespace DrupalCI\Plugin\JobTypes\generic;

use DrupalCI\Plugin\JobTypes\JobBase;

/**
 * @PluginID("generic")
 */
// ^^^ Use an annotation to define the job type name.

class GenericJob extends JobBase {
  // ^^^ Extend JobBase, to get the main test runner functionality

  /**
   * @var string
   */
  public $jobtype = 'generic';
  // ^^^ I don't believe this property is currently used; but anticipate we
  // will want to reference the jobtype from the object itself at some point.

}