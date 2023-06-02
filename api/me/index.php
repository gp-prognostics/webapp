<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/config.php');

$pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbname, $username, $password);

$headers = apache_request_headers();
$token = $headers['Authorization'];

$stmt = $pdo->prepare("SELECT username, avatarUrl FROM users WHERE token = :token");
$stmt->bindParam(':token', $token);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if ($user) {
    echo json_encode(
        array(
            'username' => $user['username'],
            'avatarUrl' => $user['avatarUrl']
        )
    );
} else {
    http_response_code(401);
    echo json_encode(array('error' => 'User not found'));
}


?>