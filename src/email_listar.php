<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = conectar_banco();

    $sql = 'SELECT
                ID,
                PESSOA_ID,
                ENDERECO_EMAIL
            FROM EMAIL
            ORDER BY ID';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $emails = $stmt->fetchAll();

    echo json_encode($emails);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao listar emails',
        'detalhe' => $e->getMessage()
    ]);
}