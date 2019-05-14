<?php
  session_start();
  setcookie("PHPSESSID" , '', 1, '/') ;  // delete cookie
  session_destroy() ;  // delete session file
  header("Location: signin.php") ;
  