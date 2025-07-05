<?php

include_once dirname(__FILE__) . "/eventsconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $eventsconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/events/");

echo $out;