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

    $sql = 'SELECT ID, NOME FROM PESSOA WHERE ID = :id';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    $stm->execute();

    $pessoa = $stm->fetch();

    if(!$pessoa) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pessoa nao encontrada']);
        exit;
    }

    echo json_encode($pessoa);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao buscar pessoa',
        'detalhe' => $e->getMessage()
    ]);
}