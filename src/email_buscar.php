<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'SELECT
                ID,
                PESSOA_ID,
                ENDERECO_EMAIL
            FROM EMAIL
            WHERE ID = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $email = $stmt->fetch();

    if (!$email) {
        http_response_code(404);
        echo json_encode(['erro' => 'Email nao encontrado']);
        exit;
    }

    echo json_encode($email);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao buscar email',
        'detalhe' => $e->getMessage()
    ]);
}