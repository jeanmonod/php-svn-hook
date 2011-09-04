<?php

// Exit by displaying an error
function errorExit($msg){
  $stderr = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');
  fwrite($stderr, "PRE COMMIT HOOK FAIL, PLEASE CONTACT SERVER ADMIN.\n ($msg)\n");
  exit(1);
}

// Read arguments and options
if (count($argv) < 3){
  errorExit("Missing arguments! Usage: script_name.php SVN_REPO SVN_TRX [ --opt]*");
}
$repo = $argv[1];
$trx = $argv[2];
$options = array();
for ($i=3; $i < count($argv); $i++){
  if (strpos($argv[$i], '--') !== 0){
    errorExit("Invalid argument [".$argv[$i]."], all options must start by '--'");
  }
  if (strpos($argv[$i], '=') === false){
    $optName = $argv[$i];
    $optValue = true;
  }
  else {
    list($optName, $optValue) = explode('=', $argv[$i]);
  }
  $options[substr($optName,2)] = $optValue;
} 
$invalid = array_diff(array_keys($options), array('test-mode'));
if (count($invalid)) {
  errorExit("Invalid option name ".json_encode($invalid));
}

// Include the SVN base functions
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'svn'.DIRECTORY_SEPARATOR.'svn_functions.'.(isset($options['test-mode'])?'test.':'').'php');

// Read the message and the file changed
$mess = svn_get_commit_message($repo, $trx);
$fileChanges = svn_get_commited_files($repo, $trx);

// Run all the script
$scriptDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'checks';
$checkWithError = array();
foreach (scandir($scriptDir) as $scriptName) {
  if (substr($scriptName,strlen($scriptName)-10)=='.class.php'  && $scriptName != "BasePreCommitCheck.class.php") {
    try {
      require_once $scriptDir.DIRECTORY_SEPARATOR.$scriptName;
      $className = substr($scriptName,0,strlen($scriptName)-10);
      $check = new $className($mess);
      $check->runCheck($fileChanges);
      if ($check->fail()){
        $checkWithError[] = $check;
      }
    }
    catch (Exception $e){
      fwrite($stderr, "PRE COMMIT HOOK FAIL, PLEASE CONTACT SERVER ADMIN.\n (Error in the subscript: $scriptName)\n");
      exit(1);
    }
  }
}

// If no error found, quit with sucess
if (count($checkWithError) == 0){
  echo "All pre commit checks successed";
  exit(0);
}

// Generate a human message with errors
$resume = "The following pre commit check fail:\n";
$detail = strtoupper("\nDetail of the checks errors:\n");
foreach ($checkWithError as $check){
  $resume .= ' * '.$check->getTitle().': '.$check->renderErrorSummary()."\n";
  $detail .= $check->getTitle().":\n".$check->renderErrorDetail()."\n".$check->renderInstructions()."\n";
}
$message = "\n\nPRE COMMIT HOOK FAIL:\n".$resume.$detail;
$stderr = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');
fwrite($stderr, $message);
exit(1);
