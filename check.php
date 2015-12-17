<?php
/**
 *  Check Login Validation
 *
 */
session_start();

// Hardcoded user/pass
if (isset($_REQUEST['user'])) {
  if ($_REQUEST['user'] == 'admin' && $_REQUEST['pass'] == 'changeme') {
    $_SESSION['loggedin'] = date('U');
  }
   else {
    //print 'bad username or password.';
   }
  }

header('location: ' . $_SERVER['HTTP_REFERER']);
