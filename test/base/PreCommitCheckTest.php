<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(8, new lime_output_color());

require_once dirname(__FILE__).'/../../checks/BasePreCommitCheck.class.php';
class PreCommitTest extends BasePreCommitCheck {
  function getTitle() { return "Test Check"; }
  function renderErrorSummary() { return "Test Error"; }
}

$c = new PreCommitTest('Comment');
$c->runCheck(array('file'=>array('line')));
$t->ok(!$c->fail(),"->runCheck() The base call is not affecting the check result");

$c = new PreCommitTest('Comment --option1');
$t->ok($c->hasOption('option1'),"->hasOption() Is working for existing option");
$t->ok(!$c->hasOption('option2'),"->hasOption() Is working for non existing option");

$c = new PreCommitTest("Comment --option1 \n--option2=toto");
$t->ok($c->getOption('option1'),"->getOption() Return true for simple option");
$t->is($c->getOption('option2'), 'toto',"->getOption() Return value for keyVal option");

$t->is(PreCommitTest::getExtension('toto.doc'), 'doc', "->getExtension() Return valid extension on basic filename");
$t->is(PreCommitTest::getExtension('toto.doc.xls'), 'xls', "->getExtension() Return valid extension on multi dot filename");
$t->is(PreCommitTest::getExtension('toto_doc'), null, "->getExtension() Return null when there is no extension");
