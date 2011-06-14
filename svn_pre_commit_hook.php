<?php

// Get the error output
$stderr = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');

// Read arguments
if (count($argv) < 3 || count($argv) > 4){
  fwrite($stderr, "PRE COMMIT HOOK FAIL, PLEASE CONTACT SERVER ADMIN.\n (Usage: script_name.php SVN_REPO SVN_TRX)\n");
  exit(1);
}
$repo = $argv[1];
$trx = $argv[2];
$testMode = isset($argv[3]) && $argv[3]=='--test-mode';

// Include the SVN base functions
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'svn'.DIRECTORY_SEPARATOR.'svn_functions.'.($testMode?'test.':'').'php');

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
fwrite($stderr, $message);
exit(1);
