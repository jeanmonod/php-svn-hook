<?php

class PreCommitManager {
  
  protected $repoName, $trxNum, $options, $checksWithError;
    
  /**
   * Parse and validate arguments and options of the pre_commit script
   * @param $args
   */
  public function parseArguments($args){
    
    // Read the RepoName and TrxNum
    if (count($args) < 2){
      throw new Exception("Missing arguments! Usage: script_name.php SVN_REPO SVN_TRX [ --opt]*");
    }
    $this->repoName = $args[0];
    $this->trxNum = $args[1];

    // Read potential options
    $this->options = array();
    for ($i=2; $i < count($args); $i++){
      if (strpos($args[$i], '--') !== 0){
        throw new Exception("Invalid argument [".$args[$i]."], all options must start by '--'");
      }
      if (strpos($args[$i], '=') === false){
        $optName = $args[$i];
        $optValue = true;
      }
      else {
        list($optName, $optValue) = explode('=', $args[$i]);
      }
      $this->options[substr($optName,2)] = $optValue;
    }

    // Reject invalid one
    $invalid = array_diff(array_keys($this->options), array('test-mode', 'key-value-option'));
    if (count($invalid)) {
      throw new Exception("Invalid option name ".json_encode($invalid));
    }
    
    return $this->options;
  }
  

  
  public function processChecks(){
    
    // Include the SVN base functions
    $svnScript = 'svn_functions.'.(isset($this->options['test-mode'])?'test.':'').'php';
    require(dirname(__FILE__).DIRECTORY_SEPARATOR.'svn'.DIRECTORY_SEPARATOR.$svnScript);

    // Read the message and the file changed
    $mess = svn_get_commit_message($this->repoName, $this->trxNum);
    $fileChanges = svn_get_commited_files($this->repoName, $this->trxNum);

    // Run all the script
    $scriptDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'checks';
    $this->checksWithError = array();
    foreach (scandir($scriptDir) as $scriptName) {
      if (substr($scriptName,strlen($scriptName)-10)=='.class.php'  && $scriptName != "BasePreCommitCheck.class.php") {
        try {
          require_once $scriptDir.DIRECTORY_SEPARATOR.$scriptName;
          $className = substr($scriptName,0,strlen($scriptName)-10);
          $check = new $className($mess);
          $check->runCheck($fileChanges);
          if ($check->fail()){
            $this->checksWithError[] = $check;
          }
        }
        catch (Exception $e){
          throw new Exception("Error in the subscript: $scriptName\n");
        }
      }
    }
  }
  
  public function allCheckPassed(){
    return is_array($this->checksWithError) && count($this->checksWithError) == 0;
  }

  public function getErrorMsg(){
    // Generate a human message with errors
    $resume = "The following pre commit check fail:\n";
    $detail = strtoupper("\nDetail of the checks errors:\n");
    foreach ($this->checksWithError as $check){
      $resume .= ' * '.$check->getTitle().': '.$check->renderErrorSummary()."\n";
      $detail .= $check->getTitle().":\n".$check->renderErrorDetail()."\n".$check->renderInstructions()."\n";
    }
    return "\n\nPRE COMMIT HOOK FAIL:\n".$resume.$detail;
  }
   
}