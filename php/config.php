<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'saep_db');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
date_default_timezone_set('America/Sao_Paulo');
?>