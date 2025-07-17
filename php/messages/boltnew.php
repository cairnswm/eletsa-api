<?php

include_once dirname(__FILE__) . "/messagesconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $messagesconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/messages/");

echo $out;