<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/config.php');

$pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbname, $username, $password);

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


// Working code : 
// $username = "LibeloDebug";
// $id = "100000000000000000";
// $avatarUrl = "vod4kdmn30fp3kg";
// $token = "secretTokenDebug";

// $sql = "INSERT INTO users (username, id, avatarUrl, token) VALUES ('$username', '$id', '$avatarUrl', '$token')";

// $query = $pdo->prepare($sql);
// $query->execute();

// echo 'Debug data inserted';


// Not working code :
// $connection = 'SELECT * FROM users WHERE discord_id = :discord_id';
// $query = $pdo->prepare($connection);
// $query->execute([
  //     'discord_id' => $discord_id
  // ]);
  // $user = $query->fetch(PDO::FETCH_ASSOC);
  
  // if (!$user) {
    //   $sql = 'INSERT INTO users (discord_id, discord_username, avatarUrl) VALUES (:discord_id, :discord_username, :avatarUrl)';
    //   $query = $pdo->prepare($sql);
    //   $token = hash('sha256', $discord_id . time() . bin2hex(random_bytes(16)));
    //   $query->execute([
      //       'id' => $discord_id,
      //       'token' => $token,
      //       'avatarUrl' => $avatarUrl,
      //       'username' => $discord_username,
      //   ]);
      // }
      // else {
        //   $sql = 'SELECT token FROM users WHERE discord_id = :discord_id';
        //   $query = $pdo->prepare($sql);
        //   $query->execute([
          //       'discord_id' => $discord_id
          //   ]);
          //   $user = $query->fetch(PDO::FETCH_ASSOC);
          //   $token = $user['token'];
          // }
          
$sql = 'SELECT * FROM users WHERE id = :discord_id';
$query = $pdo->prepare($sql);
$query->execute([
    'id' => $_GET['id']
]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!isset($user)){
  // creare usern
}

echo json_encode(array(
  "status" => "success",
  "token" => $token
));