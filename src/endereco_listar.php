<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = conectar_banco();

    $sql = 'SELECT
                ID_ENDERECO,
                ID_PESSOA,
                ID_CIDADE,
                CEP,
                RUA,
                BAIRRO,
                NUMERO
            FROM ENDERECO
            ORDER BY ID_ENDERECO';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $enderecos = $stmt->fetchAll();

    echo json_encode($enderecos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao listar enderecos',
        'detalhe' => $e->getMessage()
    ]);
}