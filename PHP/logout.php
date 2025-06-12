<?php
require_once 'classes/Session.php';

Session::destroy();
header("Location: index.php");
exit;
?>