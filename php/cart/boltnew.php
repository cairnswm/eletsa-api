<?php

include_once dirname(__FILE__) . "/cartconfig.php";
include_once dirname(__FILE__) . "/../gapiv2/generateboltconfig.php";

$config = $cartconfig;

$out = generateApiDoc($config, "https://eletsa.cairns.co.za/php/cart/");

echo $out;