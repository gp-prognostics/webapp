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
    'redirect_uri' => 'https://beta.gp-prognostics.fr/login',
    'scope' => 'identify'
];
echo json_encode($payload);

$playload_string = http_build_query($payload);
$discord_token_url = 'https://discordapp.com/api/oauth2/token';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $playload_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);
echo $response;

?>
