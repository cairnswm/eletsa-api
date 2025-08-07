<?php

include_once dirname(__FILE__) . "/activityconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $activityconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/activity/");

echo $out;