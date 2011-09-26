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
    $match_total = preg_match_all("/(^|\s+)(#\d+)(:|,|\s+|$)/", $comment, $matches);
    if ( $match_total == 0 ) {
      return "Impossible to find any ticket reference in the commit message";
    }
  }
  
  public function renderInstructions(){
    return "If you want to force commit without referring any ticket, add the parameter --no-ticket in your comment";
  }
  
}
