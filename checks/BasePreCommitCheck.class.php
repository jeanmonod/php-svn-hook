<?php

abstract class BasePreCommitCheck {
  
  protected $svnComment;
  protected $globalError = array();
  protected $codeError = array();
  protected $options = array();

  public function __construct($svnComment=''){
    $this->svnComment = $svnComment;
    $this->parseOptions();
  }
  
  abstract function getTitle();
  
  abstract function renderErrorSummary();
  
  public function runCheck($svnCommitedFiles){
    
    // Check on the comment
    $result = $this->checkSvnComment($this->svnComment);
    if ($result !== null){
      $this->globalError[] = $result;
    }
    
    // Check on the files
    foreach ($svnCommitedFiles as $filename => $lines){

      //Check the entire content
      if($fileResult = $this->checkFullFile($lines, $filename)){
        $this->globalError[] = $fileResult;
      }
      
      //Check line by line
      foreach ($lines as $pos => $line){
        $result = $this->checkFileLine($filename, $pos, $line);
        if ($result !== null){
          $this->codeError[$filename.':'.($pos+1)] = $result;
        }
      }
    } 
  }
  
  public function fail() {
    return count($this->globalError) > 0 || count($this->codeError) > 0;   
  }
  
  public function checkSvnComment($comment){
  }
  
  public function checkFileLine($file, $pos, $content){
  }
  
  public function checkFullFile($lines, $filename){
  }

  
  public function renderErrorDetail(){
    $details = implode("\n",$this->globalError);
    foreach ($this->codeError as $position => $error){
      $details .= $position . ' ' . $error . "\n";
    }
    return $details;
  }
  
  public function renderInstructions(){
    return "";
  }
  
  public function hasOption($name){
    return isset($this->options[$name]);
  }
  
  public function getOption($name){
    if (!$this->hasOption($name)){
      throw new Exception("Option [$name] does not exist"); 
    }
    return $this->options[$name];
  }
  
  protected function parseOptions() {
    preg_match_all('/\-\-([^\s]+)/', $this->svnComment, $matches);
    foreach ($matches[1] as $option) {
      $option = explode('=', $option);
      $this->options[$option[0]] = isset($option[1]) ? $option[1] : true;
    }
  }
  
  /**
   * Return extension of a given file
   * @param string $filename
   * @return string or null
   */
  public static function getExtension($filename){
    preg_match("@^.*\.([^\.]*)$@", $filename, $match);
    return isset($match[1]) ? $match[1] : null;
  }
  
}