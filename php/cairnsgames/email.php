<?php

include_once __DIR__ . '/permissionfunctions.php';

function sendEmailByTemplate($to_user_id, $templateName, $data) {
  $appId = "e671937d-54c9-11f0-9ec0-1a220d8ac2c9";
  $apikey = getSecretWithAppId($appId, 'emailer_api_key',null);
  if (!$apikey) {
    throw new Exception("API key not found.");
  }
  $url = "https://cairnsgames.co.za/php/emailer2/emailbytemplate.php";
  $postFields = [
    "to_user_id" => $to_user_id,
    "template_name" => $templateName,
    "data" => $data
  ];
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($postFields),
    CURLOPT_HTTPHEADER => [
      "apikey: $apikey",
      "app_id: $appId",
      "user-agent: vscode-restclient"
    ],
  ]);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  if ($err) {
    return "cURL Error #:" . $err;
  }
  return $response;
}