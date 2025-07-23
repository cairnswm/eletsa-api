<?php

include_once dirname(__FILE__) . "/ticketsconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $ticketsconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/tickets/");

echo $out;