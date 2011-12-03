<?php

// Function to execute the script in a controle way ( code taken from symfony1.4 sfFilesystem::execute() )
function execute($cmd, &$output, &$error, &$returnCode){
  $descriptorspec = array(
    1 => array('pipe', 'w'), // stdout
    2 => array('pipe', 'w'), // stderr
  );
  $process = proc_open($cmd, $descriptorspec, $pipes);
  if (!is_resource($process)) {
    throw new RuntimeException("Unable to execute the command. [$cmd]");
  }
  stream_set_blocking($pipes[1], false);
  stream_set_blocking($pipes[2], false);
  $output = $error = '';
  foreach ($pipes as $key => $pipe) {
    while (!feof($pipe)) {
      if (!$line = fread($pipe, 128)){
        continue;
      }
      if (1 == $key) {
        $output .= $line; // stdout
      }
      else {
        $error .= $line; // stderr
      }
    }
    fclose($pipe);
  }
  $returnCode = proc_close($process);
}



// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(8, new lime_output_color());
$scriptPath = realpath(dirname(__FILE__).'/../../svn_pre_commit_hook.php');


// Test calling the script in an invalid way
execute("php $scriptPath repoName trxNum --toto", $output, $error, $returnCode);
$t->is($returnCode, 1, "Script fail as two arguments are required");
$t->is($error, "PRE COMMIT HOOK SYSTEM ERROR, PLEASE CONTACT SERVER ADMIN.\n (Invalid option name [\"toto\"])\n", "Valid error message is return");


// First test with a working commit
$cmd = "php $scriptPath repoName trxNum --test-mode --include=EmptyComment";
execute($cmd, $output, $error, $returnCode);
$t->is($returnCode, 0, "On success, return code is 0");
$t->is($output, "All pre commit checks successed", "On success a success message is return");
$t->is($error, "", "On success, no error output on stderr");


// Second test with a fail commit, due to an empty comment
$cmd = "php $scriptPath emptyComment trxNum --test-mode --include=EmptyComment";
execute($cmd, $output, $error, $returnCode);
$t->is($returnCode, 1, "On error, return code is 1");
$t->is($output, "", "On error, no echo on the stdout");
$errorMsg = <<< EOC


PRE COMMIT HOOK FAIL:
The following pre commit check fail:
 * Reject minimalistic comment: Commit message empty or too short

DETAIL OF THE CHECKS ERRORS:
Reject minimalistic comment:
Commit message has been rejected (too short). Please provide more details about changes you want to commit.


EOC;
$t->is($error, $errorMsg, "A well formated message is generated on stderr");
