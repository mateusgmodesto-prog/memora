<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = conectar_banco();

    $sql = 'SELECT
                ID_CIDADE,
                CIDADE,
                ESTADO
            FROM CIDADE
            ORDER BY ID_CIDADE';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $cidades = $stmt->fetchAll();

    echo json_encode($cidades);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao listar cidades',
        'detalhe' => $e->getMessage()
    ]);
}