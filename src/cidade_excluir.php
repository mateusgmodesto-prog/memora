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

    $sql = 'DELETE FROM CIDADE WHERE ID_CIDADE = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Cidade nao encontrada']);
        exit;
    }

    http_response_code(202);
    echo json_encode([
        'mensagem' => 'Cidade excluida com sucesso',
        'ID_CIDADE' => (int) $id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao excluir cidade',
        'detalhe' => $e->getMessage()
    ]);
}