<?php


function svn_get_commit_message($repo, $trx) {
  if ($repo == 'emptyComment') {
    return "";
  }
  return <<< EOM
Fake commit comment including:
 * Nothing
 * Nothing else
fix #234
EOM;
}

function svn_get_commited_files($repo, $trx) {
  return array(
    'path/file1' => array('line 1', 'line 2', 'last line of code'),
    'path/file2' => array('first line', 'last line of code')
  );  
}