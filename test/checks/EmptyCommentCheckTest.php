<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(5, new lime_output_color());

// Load dependency
include_once dirname(__FILE__).'/../../checks/EmptyCommentCheck.class.php';

$c = new EmptyCommentCheck('Simple file with no tab');
$c->runCheck(array('file1'=>array('line1', 'line2')));
$t->ok(!$c->fail(),"The check is not failling if there is no tab in the files");
  
$c = new EmptyCommentCheck('toto');
$c->runCheck(array());
$t->ok($c->fail(),"The check works when comment is to small");
$t->is($c->renderErrorSummary(), "Commit message empty or too short", "A valid summary message is return");
$t->is($c->renderErrorDetail(), "Minimum size is 5 characters", "A valid detail message is return");

$c = new EmptyCommentCheck('Long comment');
$c->runCheck(array());
$t->ok(!$c->fail(),"The check is working when comment is long enought");
