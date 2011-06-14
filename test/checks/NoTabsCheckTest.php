<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(8, new lime_output_color());

// Load dependency
include_once dirname(__FILE__).'/../../checks/NoTabsCheck.class.php';

$phpFilename = 'file1.php';
$phpFilename2 = 'file2.php';
$txtFilename = 'toto.txt';

// Simple file with no tab
$c = new NoTabsCheck();
$c->runCheck(array($phpFilename=>array('line1', 'line2')));
$t->ok(!$c->fail(),"The check is not failling if there is no tab in the files");
  
// Simple file with tab
$c = new NoTabsCheck();
$c->runCheck(array($phpFilename=>array("line1", "\tline2", "line3\t")));
$t->ok($c->fail(),"The check works when there is a tab in the file");
$t->is($c->renderErrorSummary(), "2 tabs found", "A valid summary message is return");
$t->is($c->renderErrorDetail(), "$phpFilename:2 Char 0 is a tab\n$phpFilename:3 Char 5 is a tab\n", "A valid detail message is return");

// Simple file with tab but options --allow-tabs
$c = new NoTabsCheck('comment --allow-tabs');
$c->runCheck(array($phpFilename=>array("line1", "\tline2", "line3\t")));
$t->ok(!$c->fail(),"The check is ommited when there is the option --alow-tabs");

// Simple file but with txt extension
$c = new NoTabsCheck();
$c->runCheck(array($txtFilename=>array("line1", "\tline2", "line3\t")));
$t->ok(!$c->fail(),"The check is ommited when file extention is not in ".json_encode($c->extensionsToCheck));

// Two files containings tabs
$c = new NoTabsCheck();
$c->runCheck(array(
  $phpFilename=>array("line1", "\tline2", "line3\t"),
  $phpFilename2=>array("line1", "line2", "\tline3", "line4"),
));
$t->is($c->renderErrorSummary(), "3 tabs found", "A valid summary message is return when there is tabs in multiple files");
$t->is($c->renderErrorDetail(), "$phpFilename:2 Char 0 is a tab\n$phpFilename:3 Char 5 is a tab\n$phpFilename2:3 Char 0 is a tab\n", "A valid detail message is return when there is tabs in multiple files");

