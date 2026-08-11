<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($metodo === 'GET' && empty($id)) {
    require 'cidade_listar.php';
    exit;
}

if ($metodo === 'GET' && !empty($id)) {
    require 'cidade_buscar.php';
    exit;
}

if ($metodo === 'POST') {
    require 'cidade_criar.php';
    exit;
}

if ($metodo === 'PUT') {
    require 'cidade_atualizar.php';
    exit;
}

if ($metodo === 'DELETE') {
    require 'cidade_excluir.php';
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Metodo nao permitido']);
