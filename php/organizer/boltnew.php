<?php

include_once dirname(__FILE__) . "/organizerconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $organizersconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/organizer/");

echo $out;