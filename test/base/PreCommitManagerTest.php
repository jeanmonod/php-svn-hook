<?php 

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(7, new lime_output_color());
require_once dirname(__FILE__).'/../../PreCommitManager.class.php';
$manager = new PreCommitManager();


// Test arguments parsing
$test = "->parseArguments(): Script should fail when the two required args are not pprovide";
try { $manager->parseArguments(array("repoName")); $t->fail($test); }
catch (Exception $e) {
  $t->pass($test);
  $t->is($e->getMessage(), "Missing arguments! Usage: script_name.php SVN_REPO SVN_TRX [ --opt]*", $test . ": Appropriate Exception msg sent");
}
$test = "->parseArguments(): Fail as third arg must be an option stating by --*";
try { $manager->parseArguments(array("repoName", "trxNum",  "invalidArg")); $t->fail($test); }
catch (Exception $e) {
  $t->pass($test);
  $t->is($e->getMessage(), "Invalid argument [invalidArg], all options must start by '--'", $test . ": Appropriate Exception msg sent");
}
$test = "->parseArguments(): Fail as only a subset of options are allow";
try { $manager->parseArguments(array("repoName", "trxNum", "--invalidOpt")); $t->fail($test); }
catch (Exception $e) {
  $t->pass($test);
  $t->is($e->getMessage(), "Invalid option name [\"invalidOpt\"]", $test . ": Appropriate Exception msg sent");
}
$options = $manager->parseArguments(array("repoName", "trxNum", "--test-mode", "--key-value-option=123"));
$t->is($options, array("test-mode"=>true, "key-value-option"=>"123"), "->parseArguments() Valid options are well parsed");