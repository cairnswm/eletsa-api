<?php

include_once dirname(__FILE__) . "/followconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $followconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/follow/");

echo $out;