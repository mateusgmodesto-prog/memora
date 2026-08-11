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
                ID_CIDADE,
                CIDADE,
                ESTADO
            FROM CIDADE
            WHERE ID_CIDADE = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $cidade = $stmt->fetch();

    if (!$cidade) {
        http_response_code(404);
        echo json_encode(['erro' => 'Cidade nao encontrada']);
        exit;
    }

    echo json_encode($cidade);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao buscar cidade',
        'detalhe' => $e->getMessage()
    ]);
}