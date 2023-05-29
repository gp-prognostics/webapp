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
  CURLOPT_URL =>  $discord_user_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => $header
));

$response = curl_exec($curl);
curl_close($curl);

if (!isset($response['id']) ){
  echo curl_error($curl);
}

echo $response;

$response = json_decode($response, true);


$sql = "SELECT * FROM users WHERE discord_id = " . $response['id'];
$result = $conn->query($sql);

if ($result->num_rows == 0) {
  $now = date("Y-m-d H:i:s");
  $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16);
  $user_token = hash('sha256', $response['id'] + '.' + $now + '.' + $randomString);
  $sql = "INSERT INTO users (id, token, avatarUrl, username) VALUES (" . $response['id'] . ", '" . $user_token . "', '" . $response['avatar'] . "', '" . $response['global_name'] . "')";
  $result = $conn->query($sql);
} else {
  $sql = "SELECT token FROM users WHERE discord_id = " . $response['id'];
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $user_token = $row['token'];
}

$data = array(
  "token" => $user_token,
  "connected" => true
);

echo json_encode($data);

?>
