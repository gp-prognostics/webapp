<?php

if (!isset($_POST['code'])) {
    $json = array(
        "status" => "error",
        "message" => "No code provided"
    );
    echo json_encode($json);
    exit();
}

$code = $_POST['code'];

$payload = [
    'code' => $code,
    'client_id' => '1111955711665127535',
    'client_secret' => 'fhHNfcMuDa4VaItocUqC7ipqN_CAOKPB',
    'grant_type' => 'authorization_code',
    'redirect_uri' => 'https://beta.gp-prognostics.fr/login',
    'scope' => 'identify'
];

echo json_encode($payload);

?>
