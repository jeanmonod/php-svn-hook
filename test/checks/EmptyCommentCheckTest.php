<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(8, new lime_output_color());

// Load dependency
include_once dirname(__FILE__).'/../../checks/EmptyCommentCheck.class.php';

$c = new EmptyCommentCheck('Normal commit message');
$c->runCheck(array('file1'=>array('line1', 'line2')));
$t->ok(!$c->fail(),"The check is not failling if there is a good commit msg");
  
$c = new EmptyCommentCheck('toto');
$c->runCheck(array());
$t->ok($c->fail(),"The check fails when comment msg is too small");
$t->is($c->renderErrorSummary(), "Commit message empty or too short", "A valid summary message is return");
$t->is($c->renderErrorDetail(), "Commit message has been rejected (too short). Please provide more details about changes you want to commit.", "A valid detail message is return");

$c = new EmptyCommentCheck("--allow-tabs --no-ticket\n--any-other-option,--unusual-comma-separator");
$c->runCheck(array());
$t->ok($c->fail(), "The check fails when comment msg only contains php-svn-hook parameters");

$c = new EmptyCommentCheck("Fix #430\n\n--allow-tabs");
$c->runCheck(array());
$t->ok(!$c->fail(), "The check is not failing when comment msg is long enough, even when parameters are ignored");

$c = new EmptyCommentCheck("\n \n!\n \n");
$c->runCheck(array());
$t->ok($c->fail(), "The check fails when comment msg does not contain any word");

$c = new EmptyCommentCheck("\n \n\n \n--no-ticket");
$c->runCheck(array());
$t->ok($c->fail(), "The check fails when comment msg contains only blank characters and a parameter");

