<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(5, new lime_output_color());

// Load dependency
include_once dirname(__FILE__).'/../../checks/TicketReferenceCheck.class.php';

$c = new TicketReferenceCheck('Commit with One Ticket Number, refs #1234');
$c->runCheck(array());
$t->ok(!$c->fail(), "Valid commit with One Ticket reference");
 
$c = new TicketReferenceCheck('Closes #62373. Commit with Three Ticket Numbers (see also #1 and #387)');
$c->runCheck(array());
$t->ok(!$c->fail(), "Valid commit with Three Ticket references");

$c = new TicketReferenceCheck('Commit with One Ticket Number, but without # char. Ticket 927');
$c->runCheck(array());
$t->ok($c->fail(), "Invalid commit with One Ticket Number, but without # char");

$c = new TicketReferenceCheck('Commit without reference to any ticket');
$c->runCheck(array());
$t->ok($c->fail(), "Invalid commit because no ticket is referenced.");

$c = new TicketReferenceCheck('Forced commit without reference to any ticket.\n--no-ticket');
$c->runCheck(array());
$t->ok(!$c->fail(), "Check skipped if option --no-ticket is given");
