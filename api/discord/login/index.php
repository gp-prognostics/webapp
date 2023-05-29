<?php

if (!isset($_GET['code'])) {
    $json = array(
        "status" => "error",
        "message" => "No code provided"
    );
    http_response_code(400);
    echo json_encode($json);
    exit();
}

$code = $_GET['code'];

$payload = [
    'code' => $code,
    'client_id' => '1111955711665127535',
    'client_secret' => 'fhHNfcMuDa4VaItocUqC7ipqN_CAOKPB',
    'grant_type' => 'authorization_code',
    'redirect_uri' => 'https://beta.gp-prognostics.fr/login/',
    'scope' => 'identify'
];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://discordapp.com/api/oauth2/token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => http_build_query($payload),
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>
