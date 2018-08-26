<?php
require "./REST.php";
$rest = new REST();
$data = $rest->setTheMiddleNumber('15119396663','18520260304','18925683219');
exit(json_encode($data));