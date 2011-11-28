<?php

// Init lime
include_once dirname(__FILE__).'/../lime/lime.php';
$t = new lime_test(19, new lime_output_color());

// Load dependency
include_once dirname(__FILE__).'/../../checks/TicketReferenceCheck.class.php';

$c = new TicketReferenceCheck("Commit with One Ticket Number, refs #1234");
$c->runCheck(array());
$t->ok(!$c->fail(), "Valid commit with One Ticket reference");
 
$c = new TicketReferenceCheck("Closes #62373. Commit with Three Ticket Numbers (see also #1 and #387)");
$c->runCheck(array());
$t->ok(!$c->fail(), "Valid commit with Three Ticket references");

$c = new TicketReferenceCheck("Commit with One Ticket Number, but without # char. Ticket 927");
$c->runCheck(array());
$t->ok($c->fail(), "Invalid commit with One Ticket Number, but without # char");

$c = new TicketReferenceCheck("Commit without reference to any ticket");
$c->runCheck(array());
$t->ok($c->fail(), "Invalid commit because no ticket is referenced.");

$c = new TicketReferenceCheck("Forced commit without reference to any ticket.\n--no-ticket");
$c->runCheck(array());
$t->ok(!$c->fail(), "Check skipped if option --no-ticket is given");

$c = new TicketReferenceCheck("see http://www.test.com/cms#1-page");
$c->runCheck(array());
$t->ok($c->fail(), "Invalid commit with a #<number> pattern part of an URL");

$c = new TicketReferenceCheck("see also http://www.split.me/#146-2");
$c->runCheck(array());
$t->ok($c->fail(), "Another Invalid commit with a #<number> pattern part of an URL");

$c = new TicketReferenceCheck("Workaround to known issue https://github.com/rails/rails#666\n\nFix PR #4");
$c->runCheck(array());
$t->ok(!$c->fail(), "Valid commit with two #<number> patterns, once in an URL, but also in a ticket reference");

$c = new TicketReferenceCheck("#1234");
$c->runCheck(array());
$t->ok(!$c->fail(), "Very short comment, without spaces, without any keyword");

$c = new TicketReferenceCheck("fix #1");
$c->runCheck(array());
$t->ok(!$c->fail(), "Single line comment with nothing AFTER #<number> pattern");

$c = new TicketReferenceCheck("#9 fixed");
$c->runCheck(array());
$t->ok(!$c->fail(), "Single line comment with nothing BEFORE #<number> pattern");

$c = new TicketReferenceCheck("Fixed issues:\n- #331\n* #793\nOne more line");
$c->runCheck(array());
$t->ok(!$c->fail(), "Multi-line comment for regexp corner cases");

$c = new TicketReferenceCheck("--no-tabs\n#178");
$c->runCheck(array());
$t->ok(!$c->fail(), "Ticket reference just after a line break");

$c = new TicketReferenceCheck("fix#5932");
$c->runCheck(array());
$t->ok(!$c->fail(), "Ugly, but valid: Words can be collated to ticket reference without any space");

$c = new TicketReferenceCheck("#5932&#921#5821-#453");
$c->runCheck(array());
$t->ok(!$c->fail(), "Ugly, but valid: A sequence of Ticket references without any separator, except # prefixes.");

$c = new TicketReferenceCheck("Feature #93: first prototype");
$c->runCheck(array());
$t->ok(!$c->fail(), "Some separators can be collated to the right side, like ':' in 'ref #575: Apply workaround...'");

$c = new TicketReferenceCheck("Fix #33, but code refactoring still needed");
$c->runCheck(array());
$t->ok(!$c->fail(), "Some separators can be collated to the right side, like ',' in 'Fix #33, but...'");

$c = new TicketReferenceCheck("Fix a typo in i18n file, that resolves #59321.");
$c->runCheck(array());
$t->ok(!$c->fail(), "Some separators can be collated to the right side, like '.' in '... that fixes #5322.'");

$c = new TicketReferenceCheck("Redmine commit message style (#343, #2100)");
$c->runCheck(array());
$t->ok(!$c->fail(), "Ticket references can be enclosed between parentheses (...)");

