<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BasePreCommitCheck.class.php';

class TicketReferenceCheck extends BasePreCommitCheck {
  
  function getTitle(){
    return "Reject commit that does not mention any ticket number reference";
  }
  
  public function renderErrorSummary(){
    return "No ticket number given.";
  }
  
  public function checkSvnComment($comment){
    if ( $this->hasOption('no-ticket') ){
      return;
    }
    $match_total = preg_match_all("/#\d+/", $comment, $matches);
    if ( $match_total == 0 ) {
      return "Basic regexp check failed to find any ticket number";
    }
    // TODO: check ticket number found against Redmine Issues...
  }
  
  public function renderInstructions(){
    return "If you want to force commit without referring any ticket, add the parameter --no-ticket in your comment";
  }
  
}
