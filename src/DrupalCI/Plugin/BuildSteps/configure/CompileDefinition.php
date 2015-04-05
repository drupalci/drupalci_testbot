<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\configure\CompileDefinition
 *
 * Compiles a complete job definition from a hierarchy of sources.
 * This hierarchy is defined as follows, which each level overriding the previous:
 * 1. Out-of-the-box DrupalCI defaults
 * 2. Local overrides defined in ~/.drupalci/config
 * 3. 'DCI_' namespaced environment variable overrides
 * 4. Test-specific overrides passed inside a DrupalCI test definition (e.g. .drupalci.yml)
 * 5. Custom overrides located inside a test definition defined via the $source variable when calling this function.
 */

namespace DrupalCI\Plugin\BuildSteps\configure;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\JobTypes\JobInterface;
use DrupalCI\Plugin\PluginBase;
use DrupalCI\Console\Helpers\ConfigHelper;
use DrupalCI\Plugin\PluginManager;
use Symfony\Component\Yaml\Yaml;

/**
 * @PluginID("compile_definition")
 */
class CompileDefinition extends PluginBase {

  /**
   * @var \DrupalCI\Plugin\PluginManager;
   */
  protected $pluginManager;

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data = NULL) {
    Output::writeLn("<info>Calculating job definition</info>");
    // Get and parse the default definition template (containing %DCI_*%
    // placeholders) into the job definition.

    // For 'generic' jobs, this is the file passed in on the
    // 'drupalci run <filename>' command; and should be fully populated (though
    // template placeholders *can* be supported).

    // For other 'jobtype' jobs, this is the file located at
    // DrupalCI/Plugin/JobTypes/<jobtype>/drupalci.yml.
    if (!$definition = $this->loadYaml($job->getDefinitionFile())) {
      $job->errorOutput('Error', 'Failed to load job definition YAML');
    }
    // Get and parse external (i.e. anything not from the default definition
    // file) job argument parameters.  DrupalCI jobs are controlled via a
    // hierarchy of configuration settings, which define the behaviour of the
    // platform while running DrupalCI jobs.  This hierarchy is defined as
    // follows, which each level overriding the previous:

    // 1. Out-of-the-box DrupalCI platform defaults, as defined in DrupalCI/Plugin/JobTypes/JobBase->platformDefaults
    $platform_defaults = $job->getPlatformDefaults();
    if (!empty($platform_defaults)) {
      Output::writeLn("<comment>Loading DrupalCI platform default arguments:</comment>");
      Output::writeLn(implode(",", array_keys($platform_defaults)));
    }

    // 2. Out-of-the-box DrupalCI JobType defaults, as defined in DrupalCI/Plugin/JobTypes/<jobtype>->defaultArguments
    $jobtype_defaults = $job->getDefaultArguments();
    if (!empty($jobtype_defaults)) {
      Output::writeLn("<comment>Loading job type default arguments:</comment>");
      Output::writeLn(implode(",", array_keys($jobtype_defaults)));
    }

    // 3. Local overrides defined in ~/.drupalci/config
    $confighelper = new ConfigHelper();
    $local_overrides = $confighelper->getCurrentConfigSetParsed();
    if (!empty($local_overrides)) {
      Output::writeLn("<comment>Loading local DrupalCI environment config override arguments.</comment>");
      Output::writeLn(implode(",", array_keys($local_overrides)));
    }

    // 4. 'DCI_' namespaced environment variable overrides
    $environment_variables = $confighelper->getCurrentEnvVars();
    if (!empty($environment_variables)) {
      Output::writeLn("<comment>Loading local namespaced environment variable override arguments.</comment>");
      Output::writeLn(implode(",", array_keys($environment_variables)));
    }

    // 5. Additional variables passed in via the command line
    // TODO: Not yet implemented
    $cli_variables = ['DCI_JOBBUILDID' => $job->getBuildID()];

    // Combine the above to generate the final array of DCI_* key=>value pairs
    $dci_variables = $cli_variables + $environment_variables + $local_overrides + $jobtype_defaults + $platform_defaults;

    $replacements = [];
    $plugin_manager = $this->getPreprocessPluginManager();
    foreach ($dci_variables as $key => $value) {
      if (preg_match('/^DCI_(.+)$/', $key, $matches)) {
        $name = strtolower($matches[1]);
        if ($plugin_manager->hasPlugin('variable', $name)) {
          /** @var \DrupalCI\Plugin\Preprocess\VariableInterface $plugin */
          $plugin = $plugin_manager->getPlugin('variable', $name);
          // @TODO: perhaps this should be on the annotation.
          $new_keys = $plugin->target();
          if (!is_array($new_keys)) {
            $new_keys = [$new_keys];
          }
          // @TODO: error handling.
          foreach ($new_keys as $new_key) {
            // Only process variable plugins if the variable being changed actually exists.
            if (!empty($dci_variables[$new_key])) {
              $dci_variables[$new_key] = $plugin->process($dci_variables[$new_key], $value, $new_key);
            }
          }
        }
      }
    }
    // Foreach DCI_* pair in the array, check if a plugin exists, and process if it does.  (Pass in test definition template)
    foreach ($dci_variables as $key => $value) {
      if (preg_match('/^DCI_(.+)$/', $key, $matches)) {
        $name = strtolower($matches[1]);
        $replacements["%$key%"] = $value;
        if ($plugin_manager->hasPlugin('definition', $name)) {
          $plugin_manager->getPlugin('definition', $name)
            ->process($definition, $value);
        }
      }
    }

    // Process DCI_* variable substitution into test definition template

    array_walk_recursive($definition, function (&$value) use ($replacements) {
      $value = strtr($value, $replacements);
    });
    $job->setBuildVars($dci_variables + $job->getBuildVars());
    $job->setDefinition($definition);
    return;
  }

  protected function loadYaml($source) {
    if ($content = file_get_contents($source)) {
      return Yaml::parse($content);
    }
    return [];
  }

  protected function getPreprocessPluginManager() {
    if (!isset($this->pluginManager)) {
      $this->pluginManager = new PluginManager('Preprocess');
    }
    return $this->pluginManager;
  }

}

    /* *************** Legacy code below *********************** */
  /*

    $confighelper = new ConfigHelper();

    // Load platform defaults
    $platform_defaults = $job->getPlatformDefaults();

    //

    $default_args = $job->getDefaultArguments();
    if (!empty($default_args)) {
      Output::writeLn("<comment>Loading build variables for this job type.</comment>");
    }

    // Load DrupalCI local config overrides
    $local_args = $confighelper->getCurrentConfigSetParsed();
    if (!empty($local_args)) {
      Output::writeLn("<comment>Loading build variables from DrupalCI local config overrides.</comment>");
    }

    // Load "DCI_ namespaced" environment variable overrides
    $environment_args = $confighelper->getCurrentEnvVars();
    if (!empty($environment_args)) {
      Output::writeLn("<comment>Loading build variables from namespaced environment variable overrides.</comment>");
    }

    // Load command line arguments
    // TODO: Routine for loading command line arguments.
    // TODO: How do we pull arguments off the drupalci command, when in a job class?
    // $cli_args = $somehelper->loadCLIargs();
    $cli_args = array();
    if (!empty($cli_args)) {
      Output::writeLn("<comment>Loading test parameters from command line arguments.</comment>");
    }

    // Create temporary config array to use in determining the definition file source
    $config = $cli_args + $environment_args + $local_args + $default_args + $platform_defaults;

    // Load any build vars defined in the job definition file
    // Retrieve test definition file
    if (isset($source)) {
      $config['explicit_source'] = $source;
    }

    $definition_file = $this->getDefinitionFile($config);
    $definition_args = array();

    // Load test definition file
    if (!empty($definition_file)) {
      Output::writeLn("<comment>Loading test parameters from build file: </comment><info>$definition_file</info>");
      $jobdef = new JobDefinition();
      $result = $jobdef->load($definition_file);
      if ($result == -1) {
        // Error loading definition file.
        $job->errorOutput("Failed", "Unable to parse build file.");
        // TODO: Robust error handling
        return;
      };
      $job_definition = $jobdef->getParameters();
      if (empty($job_definition)) {
        $job_definition = array();
        $definition_args = array();
      }
      else {
        $definition_args = !empty($job_definition['build_vars']) ? $job_definition['build_vars'] : array();
      }
      $job->setDefinition($job_definition);
    }

    $config = $cli_args + $definition_args + $environment_args + $local_args + $default_args + $platform_defaults;

    // Set initial build variables
    $buildvars = $job->getBuildVars();
    $job->setBuildVars($buildvars + $config);

    // Map relevant build variables into the job definition array
    // $this->buildvarsToDefinition($job);

    return;
  }

  protected function parseDefinitionTemplate($definition_template) {
    // TODO: YAML Parse the template file and return results.

  }

  protected function getDefinitionFile($config) {
    $definition_file = "";
    // DrupalCI file-based test definition overrides can come from a number of sources:
    // 1. A file location explicitly passed into the config function
    if (!empty($config['explicit_source'])) {
      // TODO: Validate passed filename
      $definition_file = $config['explicit_source'];
    }
    // 2. A .drupalci.yml file located in a local codebase directory
    // TODO: file_exists throws warnings if passed a 'git' URL.
    elseif (file_exists($config['DCI_CodeBase'] . ".drupalci.yml")) {
      $definition_file = $config['DCI_CodeBase'] . ".drupalci.yml";
    }
    // 3. A file location stored in the 'DCI_BuildFile' environment variable
    elseif (!empty($config['DCI_BuildFile'])) {
      $definition_file = $config['DCI_BuildFile'];
    }
    return $definition_file;
  }




  protected function buildvarsToDefinition(JobInterface $job) {
    $buildvars = $job->getBuildVars();
    $job_definition = $job->jobDefinition;

    // Process dependencies
    if (!empty($buildvars['DCI_DEPENDENCIES'])) {
      // Format: module1,module2,module3
      $dependencies = explode(',', trim($buildvars['DCI_DEPENDENCIES'], '"'));
      foreach ($dependencies as $dependency) {
        // TODO: Remove the hardcoded git.drupal.org!!!
        // Perhaps we extend this with a DrupalConfigurator class?
        $directory = 'sites/all/modules';
        // TODO: We can't assume a branch here. Need to determine the Drupal version earlier!
        $job_definition['setup']['checkout'][] = array('protocol' => 'git', 'repo' => "git://git.drupal.org/project/$dependency.git", 'branch' => 'master', 'checkout_dir' => $directory, );
      }
    }

    // Process GIT dependencies
    if (!empty($buildvars['DCI_DEPENDENCIES_GIT'])) {
      // Format: gitrepo1,branch;gitrepo2,branch;
      $dependencies = explode(';', trim($buildvars['DCI_DEPENDENCIES_GIT'], '"'));
      foreach ($dependencies as $dependency) {
        if (!empty($dependency)) {
          list($repo, $branch) = explode(',', $dependency);
          // TODO: Remove this hardcoded drupalism!!!
          $directory = 'sites/all/modules/' . basename(parse_url($repo, PHP_URL_PATH), ".git");
          $job_definition['setup']['checkout'][] = array('protocol' => 'git', 'repo' => $repo, 'branch' => $branch, 'checkout_dir' => $directory);
        }
      }
    }

    $job->job_definition = $job_definition;

  }




  // TODO: If passed a job definition source file as a command argument, pass it in to the configure function


   * Testrunner -> Config Compilation Approach (rationalize test definition file versus ENV variables)

- Mash up DCI_* ENV Variables and values from CONFIG

- Run resulting list through
	- foreach DCI_* variable
		- if hasPlugin(DCI_*) then getPlugin(DCI_*)
			- split getPlugin() into hasPlugin() and getPlugin(), where getPlugin() also calls hasPlugin().
		- Each DCI_* plugin takes the value of that environment variable and the job definition array as arguments
			- logic within each plugin expands that particular value in the parsed YAML job definition


- Then array_walk_recursive() through the YAML job definition, doing a direct substitution for any ENV variable placeholders in the definition
	- Mark ENV variable placeholders with %DCI_*%

JobType classes:
	- Need to define the default job definition array, with placeholders

DrupalCI Run:
	- Needs to take a file name OR a class name as it's argument.


   */
