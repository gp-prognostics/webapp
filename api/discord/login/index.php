<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/config.php');

$dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8";
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
$pdo = new PDO($dsn, $username, $password, $opt);


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

$response = json_decode($response, true);

$discord_id = $response['id'];
$discord_username = $response['global_name'];
$avatarUrl = $response['avatar'];

$connection = 'SELECT * FROM users WHERE discord_id = :discord_id';
$query = $pdo->prepare($connection);
$query->execute([
    'discord_id' => $discord_id
]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  $sql = 'INSERT INTO users (discord_id, discord_username, avatarUrl) VALUES (:discord_id, :discord_username, :avatarUrl)';
  $query = $pdo->prepare($sql);
  $token = hash('sha256', $discord_id . time() . bin2hex(random_bytes(16)));
  $query->execute([
      'id' => $discord_id,
      'token' => $token,
      'avatarUrl' => $avatarUrl,
      'username' => $discord_username,
  ]);
}
else {
  $sql = 'SELECT token FROM users WHERE discord_id = :discord_id';
  $query = $pdo->prepare($sql);
  $query->execute([
      'discord_id' => $discord_id
  ]);
  $user = $query->fetch(PDO::FETCH_ASSOC);
  $token = $user['token'];
}

echo json_encode(array(
  "status" => "success",
  "token" => $token
));