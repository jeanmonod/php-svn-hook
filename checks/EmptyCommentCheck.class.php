<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BasePreCommitCheck.class.php';

class EmptyCommentCheck extends BasePreCommitCheck {
  
  function getTitle(){
    return "Reject minimalistic comment";
  }
  
  public function renderErrorSummary(){
    return "Commit message empty or too short";
  }
  
  public function checkSvnComment($comment){
    if (strlen($comment) < 5){
      return "Minimum size is 5 characters";
    }
  }
  
}