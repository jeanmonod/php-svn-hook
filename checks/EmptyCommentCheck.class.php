<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BasePreCommitCheck.class.php';

class EmptyCommentCheck extends BasePreCommitCheck {
  
  function getTitle(){
    return "Reject comment";
  }
  
  public function renderErrorSummary(){
    return "Invalid";
  }
  
  public function checkSvnComment($comment){
    if (strlen($comment) < 5){
      return "Minimun size is 5 characters";
    }
  }
  
}