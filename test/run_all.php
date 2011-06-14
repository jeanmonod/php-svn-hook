<?php

require_once dirname(__FILE__).'/lime/lime.php';

$testSuite = new lime_harness();
$testSuite->register_dir(dirname(__FILE__).'/base');
$testSuite->register_dir(dirname(__FILE__).'/checks');
$testSuite->run();