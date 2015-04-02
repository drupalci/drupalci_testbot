<?php

/**
 * @file
 * Job class for SimpleTest jobs on DrupalCI.
 */

namespace DrupalCI\Plugin\JobTypes\simpletest;

use DrupalCI\Plugin\JobTypes\Component\EnvironmentValidator;
use DrupalCI\Plugin\JobTypes\JobBase;



/**
 * @PluginID("simpletest")
 */
  // ^^^ Use an annotation to define the job type name.

class SimpletestJob extends JobBase {
  // ^^^ Extend JobBase, to get the main test runner functionality

  /**
   * @var string
   */
  public $jobtype = 'simpletest';
  // I don't believe this property is currently used; but anticipate we will
  // want to reference the jobtype from the object itself at some point.


  // ****************** Start Validation related properties ******************
  /**
   * Required Arguments, which must be present in order for the job to attempt
   * to run.
   *
   * The expected format here is an array of key=>value pairs, where the key is
   * the name of a DCI_* environment variable, and the value is the array key
   * path from the parsed .yml file job definition that would need to be
   * traversed to get to the location that variable would exist in the job
   * definition.
   */
  public $requiredArguments = array(
    // DCI_DBTYPE defines the database type (mysql, pgsql, etc). In a parsed
    // yml job definition file, this information would be stored in the value
    // at array('environment' => array('db' => VALUE)); thus the traversal path
    // value is the array keys 'environment:db'
    /* Temporarily hiding to prevent validation fails while testing during development
    'DCI_DBType' => 'environment:db',
    'DCI_DBVersion' => 'environment:db',
    'DCI_PHPVersion' => 'environment:web',
    'DCI_DrupalBranch' => 'variables:DCI_DrupalBranch',
    'DCI_DBUser' => 'variables:DCI_DBUser',
    'DCI_DBPassword' => 'variables:DCI_DBPassword',
    */
  );

  /**
   * Return a list of argument variables which are relevant to this job type.
   *
   * For the Simpletest job type in the original DrupalCI proof of concept,
   * this included the following list (copied from the original BASH script).
   * Since the refresh, many of these are not currently evaluated; but they are
   * currently included here as the eventual intent is to support all of the
   * functionality that each of these provided in the original Proof of Concept
   * implementation.
   *
   * *** NEW ARGUMENTS, ADDED AFTER THE REFACTORING ***
   *
   *
   *
   * *** ORIGINAL PoC ARGUMENTS, CURRENTLY SUPPORTED ***
   *
   *
   * *** ORIGINAL PoC ARGUMENTS, NOT YET SUPPORTED ***
   *  DCI_PATCH:         Local or remote Patches to be applied.
   *       Format: patch_location,apply_dir;patch_location,apply_dir;...
   *  DCI_DEPENDENCIES:  Contrib projects to be downloaded & patched.
   *       Format: module1,module2,module2...
   *  DCI_DEPENDENCIES_GIT  Format: gitrepo1,branch;gitrepo2,branch;...
   *  DCI_DEPENDENCIES_TGZ  Format: module1_url.tgz,module1_url.tgz,...
   *  DCI_DRUPALBRANCH:  Default is '8.0.x'
   *  DCI_DRUPALVERSION: Default is '8'
   *  DCI_TESTGROUPS:    Tests to run. Default is '--class NonDefaultBlockAdmin'
   *       A list is available at the root of this project.
   *  DCI_VERBOSE:       Default is 'false'
   *  DCI_DBTYPE:        Default is 'mysql-5.5' from mysql/sqlite/pgsql
   *  DCI_DBVER:         Default is '5.5'.  Used to override the default version for a given database type.
   *  DCI_ENTRYPOINT:    Default is none. Executes other funcionality in the container prepending CMD.
   *  DCI_CMD:           Default is none. Normally use '/bin/bash' to debug the container
   *  DCI_INSTALLER:     Default is none. Try to use core non install tests.
   *  DCI_UPDATEREPO:    Force git pull of Drupal & Drush. Default is 'false'
   *  DCI_IDENTIFIER:    Automated Build Identifier. Only [a-z0-9-_.] are allowed
   *  DCI_REPODIR:       Default is 'HOME/testbotdata'
   *  DCI_DRUPALREPO:    Default is 'http://git.drupal.org/project/drupal.git'
   *  DCI_DRUSHREPO:     Default is 'https://github.com/drush-ops/drush.git'
   *  DCI_BUILDSDIR:     Default is  equal to DCI_REPODIR
   *  DCI_WORKSPACE:     Default is 'HOME/testbotdata/DCI_IDENTIFIER/'
   *  DCI_DBUSER:        Default is 'drupaltestbot'
   *  DCI_DBPASS:        Default is 'drupaltestbotpw'
   *  DCI_DBCONTAINER:   Default is 'drupaltestbot-db-mysql-5.5'
   *  DCI_PHPVERSION:    Default is '5.4'
   *  DCI_CONCURRENCY:   Default is '4'  #How many cpus to use per run
   *  DCI_RUNSCRIPT:     Command to be executed
   */
  public $availableArguments = array(
    // New arguments, added after the proof of concept
    'DCI_DieOnFail',                      // Maps to the --die-on-fail flag in run-tests.sh
    'DCI_SQLite',                         // Maps to the --sqlite flag in run-tests.sh

    // Legacy arguments, leftover from the proof of concept
    // Currently supported:
    // Not yet supported:
    'DCI_PATCH',
    'DCI_DEPENDENCIES',
    'DCI_DEPENDENCIES_GIT',
    'DCI_DEPENDENCIES_TGZ',
    'DCI_DRUPALBRANCH',
    'DCI_DRUPALVERSION',
    'DCI_TESTGROUPS',
    'DCI_VERBOSE',
    'DCI_DBTYPE',
    'DCI_DBVER',
    'DCI_ENTRYPOINT',
    'DCI_CMD',
    'DCI_INSTALLER',
    'DCI_UPDATEREPO',
    'DCI_IDENTIFIER',
    'DCI_REPODIR',
    'DCI_DRUPALREPO',
    'DCI_DRUSHREPO',
    'DCI_BUILDSDIR',
    'DCI_WORKSPACE',
    'DCI_DBUSER',
    'DCI_DBPASS',
    'DCI_DBCONTAINER',
    'DCI_PHPVERSION',
    'DCI_CONCURRENCY',
    'DCI_RUNSCRIPT',
  );

  // ******************* End Validation related properties *******************


  // **************** Start job definition related properties ****************

  public $defaultArguments = array(
    'DCI_DBVersion' => 'mysql-5.5',
    'DCI_PHPVersion' => '5.4',
    'DCI_CoreRepository' => 'git://drupalcode.org/project/drupal.git',
    'DCI_CoreBranch' => '8.0.x',
    'DCI_GitCheckoutDepth' => 1,
    'DCI_RunScript' => "/data/core/scripts/run-tests.sh ",
    'DCI_DBUser' => 'drupaltestbot',
    'DCI_DBPassword' => 'drupaltestbotpw',
    'DCI_DBURL' => 'dbtype://host', // DBVersion, DBUser and DBPassword variable plugins will change this.
    'DCI_TestGroups' => '--all'

      // %DCI_SQLite% --sqlite /tmp/.ht.sqlite --die-on-fail --php /root/.phpenv/shims/php --dburl sqlite://tmp/.ht.sqlite ban"
  );

  /**
   * @array Storage property for the default job definition array for this job type
   */
  public $defaultDefinition;

  /**
   * @return array
   */
  protected function getDefaultDefinition() {
    if (is_null($this->defaultDefinition)) {
      // Build the 'default' job definition for this job type.
      //$this->setDefaultDefinition($this->buildDefaultDefinition());
    }
    return $this->defaultDefinition;
  }

  protected function buildDefaultDefinition() {
    // TODO: Currently just returning a static array; but we may eventually
    // need to look at the items in the 'variables' key, and map them over to
    // build steps further down in the array (for example, filling out the
    // final run command.   On the other hand, this is just the 'default'
    // definition ... we should be building this further down the code path;
    // which may result in this being completely redundant.
    return array(
      'environment' => array(
        // Database type and version (defines the service container)
        // Defaults to mysql 5.5
        'db' => array(
          'mysql-5.5'
        ),
        // Requires web container (versus php-only container)
        // Defaults to PHP 5.4
        'web' => array(
          '5.4'
        ),
        // Defines any Job-specific variables which are required to build out the
        // rest of the job definition build steps, and/or configure aspects of
        // the job run.
        'variables' => array(
          'DCI_DrupalBranch' => '8.0.x',
          'DCI_DBUser' => 'drupaltestbot',
          'DCI_DBPassword' => 'drupaltestbotpw',
          'DCI_Concurrency' => '4'
        ),

        'setup' => array(
          'checkout' => array(
            'protocol' => 'git',
            'repo' => 'git://drupalcode.org/project/drupal.git',
            'branch' => '8.0.x',
            'depth' => 1,
            'checkout_dir' => './'
          ),
        ),
        'execute' => array(
          'command' => array(
            "cd /data && php /data/core/scripts/run-tests.sh --sqlite /tmp/.ht.sqlite --die-on-fail --php /root/.phpenv/shims/php --dburl sqlite://tmp/.ht.sqlite ban"
          )
        )
      )
    );


  }









  // *********************** Start legacy / unused code ***********************
  // Included here to support future development, which may leverage the logic.
  public function compatibility_bridge() {
    // TODO: This is legacy, from before the March 2015 refactoring.  Initial purpose was to maintain backwards compatibility with the Proof of Concept implementation scripts.

    // Loads items from the job definition file into environment variables in
    // order to remain compatible with the simpletest run.sh script.
    // TODO: At some point, we should deprecate non "drupalci run simpletest"
    // methods of kicking off execution of the script, which will allow us to
    // remove the validation code from the bash script itself (in favor of
    // validate step within the job classes.
    // TODO: This presumes only one db type; but may need to be expanded for multiple.
    if (empty($this->jobDefinition)) {
      return;
    }
    $definition = $this->jobDefinition['environment'];
    // We need to set a number of parameters on the command line in order to
    // prevent the bash script from overriding them
    $cmd_prefix = "";
    if (!empty($definition['db'])) {
      $dbtype = explode("-", $definition['db'][0]);
      $cmd_prefix = "DCI_DBTYPE=" . $dbtype[0] . " DCI_DBVER=" . $dbtype[1];
    }
    else {
      $cmd_prefix = "DCI_DBTYPE= DCI_DBVER= ";
    }

    $phpver = (!empty($definition['php'])) ? $definition['php'][0] : "";

    $cmd_prefix .= (!empty($phpver)) ? " DCI_PHPVERSION=$phpver " : " DCI_PHPVERSION= ";

    if (!empty($this->jobDefinition['variables'])) {
      $buildvars = $this->jobDefinition['variables'];
      foreach ($buildvars as $key => $value) {
        $cmd_prefix .= "$key=$value ";
      }
    }

    // Set working directory
    if (!empty($this->workingDirectory)) {
      $cmd_prefix .= " DCI_WORKSPACE=" . $this->workingDirectory . " ";
    }

    $this->cmd_prefix = $cmd_prefix;
  }

  protected $cmd_prefix = "";

  public function execute() {
    // TODO: This is legacy, from before the March 2015 refactoring.  Leftover from the Proof of Concept implementation.
    $cmd = "sudo " . $this->cmd_prefix . "./containers/web/run.sh";
    // Execute the simpletest testing bash script
    $this->shellCommand($cmd);
    return;
  }
  // ************************ End legacy / unused code ************************

}
