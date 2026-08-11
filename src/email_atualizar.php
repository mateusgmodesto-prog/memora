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

$pessoaId = $dados['pessoa_id'] ?? null;
$email = isset($dados['email']) ? trim($dados['email']) : null;

if (empty($id) || empty($pessoaId) || empty($email)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID, pessoa e email sao obrigatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Email invalido.']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'UPDATE EMAIL SET
                PESSOA_ID = :pessoa_id,
                ENDERECO_EMAIL = :email
            WHERE ID = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':pessoa_id', $pessoaId, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Email nao encontrado']);
        exit;
    }

    echo json_encode([
        'mensagem' => 'Email atualizado com sucesso',
        'ID' => (int) $id,
        'PESSOA_ID' => (int) $pessoaId,
        'ENDERECO_EMAIL' => $email
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao atualizar email',
        'detalhe' => $e->getMessage()
    ]);
}
