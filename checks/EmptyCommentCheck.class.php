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
    // Remove optional parameters (like --allow-tabs), 
    // in order to check the size of a meaningful message 
    $valuableComment = preg_replace('/(^|\s*)(--\S+)(\s*|$)/', '', $comment);

    // Only consider Words in the count
    $valuableComment = preg_replace('/\W+/', '', $valuableComment); 

    if (strlen($valuableComment) < 5){
      return 'Commit message has been rejected (too short). Please provide more details about changes you want to commit.';
    }
  }
  
}
