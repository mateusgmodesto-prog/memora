<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = conectar_banco();

    $sql = 'SELECT ID, NOME FROM PESSOA ORDER BY ID';
    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pessoas = $stm->fetchAll();

    echo json_encode($pessoas);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao listar pessoas',
        'detalhe' => $e->getMessage()
    ]);
}