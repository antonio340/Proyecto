<?php
// Archivo: middlewares/tokenVerify.php

// Es importante que la librería JWT esté cargada (usamos composer autoload)
require_once '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

/**
 * Función para extraer el token del header Authorization.
 * Se espera el formato "Bearer <token>"
 */
function getBearerToken() {
    // Puedes usar getallheaders() o apache_request_headers() según tu entorno
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * Función para verificar y decodificar un token JWT.
 * Retorna la data decodificada (como array) si es válido, o false en caso contrario.
 */
function verifyToken($token) {
    $secretKey = "123123o"; // Debe ser la misma clave secreta utilizada en UsuariosController
    try {
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($secretKey, 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        return false;
    }
}
?>
