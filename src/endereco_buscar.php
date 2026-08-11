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
                ID_ENDERECO,
                ID_PESSOA,
                ID_CIDADE,
                CEP,
                RUA,
                BAIRRO,
                NUMERO
            FROM ENDERECO
            WHERE ID_ENDERECO = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $endereco = $stmt->fetch();

    if (!$endereco) {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereco nao encontrado']);
        exit;
    }

    echo json_encode($endereco);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao buscar endereco',
        'detalhe' => $e->getMessage()
    ]);
}
