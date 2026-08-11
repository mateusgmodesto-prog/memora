<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use PUT.']);
    exit;
}

$id = $_GET['id'] ?? null;
$dados = json_decode(file_get_contents('php://input'), true);

$cidade = isset($dados['cidade']) ? trim($dados['cidade']) : null;
$estado = isset($dados['estado']) ? strtoupper(trim($dados['estado'])) : null;

if (empty($id) || empty($cidade) || empty($estado)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID, cidade e estado sao obrigatorios.']);
    exit;
}

if (strlen($estado) !== 2) {
    http_response_code(400);
    echo json_encode(['erro' => 'O estado deve possuir duas letras.']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'UPDATE CIDADE SET
                CIDADE = :cidade,
                ESTADO = :estado
            WHERE ID_CIDADE = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Cidade nao encontrada']);
        exit;
    }

    echo json_encode([
        'mensagem' => 'Cidade atualizada com sucesso',
        'ID_CIDADE' => (int) $id,
        'CIDADE' => $cidade,
        'ESTADO' => $estado
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao atualizar cidade',
        'detalhe' => $e->getMessage()
    ]);
}