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

    // 1) find all URL containing #<number> (protocol://server.domain.ch/path/to#<number>)
    $match_url = preg_match_all("/\w+:\/\/\S+#\d+/", $comment, $url_matches);

    // 2) find all #<number> patterns (URL patterns of point 1 included)
    $match_total = preg_match_all("/#\d+/", $comment, $matches);
    
    if ($match_total == 0) {
      return "Impossible to find any ticket reference in the commit message";
    } else if ($match_total == $match_url) {
      return "Impossible to find any ticket reference in the commit message (Note: URL are invalid references, please use #<ticket-id> syntax)";
    }
  }
  
  public function renderInstructions(){
    return "If you want to force commit without referring any ticket, add the parameter --no-ticket in your comment";
  }
  
}
