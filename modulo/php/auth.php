<?php
require './vendor/autoload.php';
use \Firebase\JWT\JWT;

$key = "e9b7f6d28e4d3a7f4691d42bdf12a4c1b17ef5a3746cf3e9173f4d8e12b7a5e8"; // Cambia esto por una clave secreta segura

function generate_jwt($user_id) {
    global $key;
    $payload = [
        'iss' => 'localhost', // Emisor del token
        'aud' => 'localhost', // Audiencia del token
        'iat' => time(), // Tiempo en que se emitió el token
        'exp' => time() + (60 * 60), // Tiempo de expiración del token (1 hora)
        'user_id' => $user_id
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function verify_jwt($token) {
    global $key;
    try {
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}
?>