<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/config.php');

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

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

if (!isset($response['access_token']) ){
  echo curl_error($curl);
}


$response = json_decode($response, true);

$token = $response['access_token'];

$discord_user_url = 'https://discordapp.com/api/users/@me';
$header = array('Authorization: Bearer ' . $token, 'Content-Type: application/x-www-form-urlencoded');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $discord_user_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => $header,
));

$response = curl_exec($curl);
curl_close($curl);

echo $response;
$response = json_decode($response, true);

$discord_id = $response['id'];
$discord_username = $response['global_name'];
$avatarUrl = $response['avatar'];

$sql = "SELECT * FROM users WHERE discord_id = '$discord_id'";
$result = $conn->query($sql);


// check if user already exists, it yes return token else create user and return token
// Token will be "discord_Id + now in UNIX timestamp + random string (16)

if ($result->num_rows > 0) {
  $token = $result->fetch_assoc()['token'];
}
else{
  $token = $discord_id . time() . bin2hex(random_bytes(16));
  $sql = "INSERT INTO users (discord_id, token, username, avatar) VALUES ('$discord_id', '$token', '$discord_username', '$avatarUrl')";
  $conn->query($sql);
}

echo json_encode(array(
  "status" => "success",
  "token" => $token
));