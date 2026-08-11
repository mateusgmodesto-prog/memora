<?php

require_once 'db.php';
header('Content-Type:  application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use PUT.']);
    exit;
}

$id = $_GET['id'] ?? null;
$dados = json_decode(file_get_contents('php://input'), true);
$nome = $dados['nome'] ?? null;
$nome = is_string($nome) ? trim($nome) : null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID e obrigatorio']);
    exit;
}

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'UPDATE PESSOA SET NOME =:nome WHERE ID = :id';
    $stm = $pdo->prepare($sql);
    $stm -> bindParam(':nome', $nome, PDO::PARAM_STR);
    $stm -> bindParam(':id', $id, PDO::PARAM_INT);
    $stm -> execute();

    if ($stm -> rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pessoa nao encontrada']);
        exit;
    }

    echo json_encode([
        'mensagem' => 'Pessoa atualizada com sucesso',
        'ID' => (int) $id,
        'NOME' => $nome
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao atualizar pessoa',
        'detalhe' => $e->getMessage()
    ]);
}