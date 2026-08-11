<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use DELETE.']);
    exit;
}

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'DELETE FROM PESSOA WHERE ID = :id';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    $stm->execute();

    if ($stm->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pessoa nao encontrada']);
        exit;
    }

    http_response_code(202);
    echo json_encode([
        'mensagem' => 'Pessoa excluida com sucesso',
        'ID' => (int) $id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao excluir pessoa',
        'detalhe' => $e->getMessage()
    ]);
}