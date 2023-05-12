<?php
include 'GateySDK.php';
$id=0;
$gateysdk = new GateySDK($id,'CLIENT KEY','SERVER KEY');

$gateysdk->capture_message("Capture me!","DEBUG");

set_exception_handler(array($gateysdk, 'catch'));
throw new Exception("This exception will be catched");

restore_exception_handler();
throw new Exception("And that won't");