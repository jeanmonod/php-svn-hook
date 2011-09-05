<?php
include_once dirname(__FILE__).'/PreCommitManager.class.php';
$stderr = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');

try {
  array_shift($argv); // Remove script name
  $manager = new PreCommitManager();
  $manager->parseArguments($argv);
  $manager->processChecks(); 
  if ($manager->allCheckPassed()) {
    echo "All pre commit checks successed";
    exit(0);
  }
  else {
    fwrite($stderr, $manager->getErrorMsg());
    exit(1);
  }
}
catch (Exception $e){
  fwrite($stderr, "PRE COMMIT HOOK SYSTEM ERROR, PLEASE CONTACT SERVER ADMIN.\n (".$e->getMessage().")\n");
  exit(1);
}