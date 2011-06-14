<?php

function svn_get_commit_message($repo, $trx) {
  exec("svnlook log -t $trx $repo", $mess);
  return implode("\n", $mess);
}

function svn_get_commited_files($repo, $trx) {
  
  // Get all changed
  exec("svnlook changed $repo --transaction $trx", $changed);
  //fwrite(STDERR, "DEBUG: Changed count:". count($changed). "\n");

  // Retrived file content
  $commitedFiles = array();
  foreach ($changed as $line){
    
    //fwrite(STDERR, "DEBUG: Change line: $line\n");
    if (in_array(substr($line,0,1), array('A', 'U'))){
      $filename = substr($line,4);
      unset($content);  // Mandatory otherwise, exec will append new content
      exec("svnlook cat $repo $filename -t $trx", $content);
      $commitedFiles[$filename] = $content;
    }
    
  }
  return $commitedFiles;
}