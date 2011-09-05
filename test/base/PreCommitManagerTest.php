<?php 

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(14, new lime_output_color());
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
$options = $manager->parseArguments(array("repoName", "trxNum", "--test-mode", "--include=123"));
$t->is($options, array("test-mode"=>true, "include"=>"123"), "->parseArguments() Valid options are well parsed");


// Test check list generation
$manager->parseArguments(array("repoName", "trxNum"));
$t->is(count($manager->getChecksToProcess()), count(scandir($manager->getCheckDirectory()))-3, "->getChecksToProcess() Return by default all the checks from the check direcorty");
$manager->parseArguments(array("repoName", "trxNum", "--include=NoTabs"));
$t->is($manager->getChecksToProcess(), array("NoTabs"), "->getChecksToProcess() With --include=XX retunr the test to inculde");
$manager->parseArguments(array("repoName", "trxNum", "--include=NoTabs:EmptyComment"));
$t->is($manager->getChecksToProcess(), array("NoTabs", "EmptyComment"), "->getChecksToProcess() With --include=XX:YY return the tests to inculde");
$manager->parseArguments(array("repoName", "trxNum", "--include=NoTabs:EmptyComment", "--exclude=EmptyComment"));
$t->is($manager->getChecksToProcess(), array("NoTabs"), "->getChecksToProcess() With --include=XX:YY, --exclude=YY remove the precedent include");
$manager->parseArguments(array("repoName", "trxNum", "--exclude=EmptyComment"));
$t->is(count($manager->getChecksToProcess()), count(scandir($manager->getCheckDirectory()))-4, "->getChecksToProcess() With --exclude=YY, remove a test");
$test = "->getChecksToProcess() exclude invalid tests throw an exception";
$manager->parseArguments(array("repoName", "trxNum", "--exclude=tata:toto:NoTabs"));
try { $manager->getChecksToProcess(); $t->fail($test); }
catch (Exception $e) {
  $t->pass($test);
  $t->is($e->getMessage(), "Invalid check to exculde: [\"tata\",\"toto\"]", $test . ": Appropriate Exception msg sent");
}

