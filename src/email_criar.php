<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$pessoaId = $dados['pessoa_id'] ?? null;
$email = isset($dados['email']) ? trim($dados['email']) : null;

if (empty($pessoaId) || empty($email)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Pessoa e email sao obrigatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Email invalido.']);
    exit;
}

try {
    $pdo = conectar_banco();
    $pdo->beginTransaction();

    $consultaId = $pdo->query(
        'SELECT COALESCE(MAX(ID), 0) + 1 AS NOVO_ID FROM EMAIL'
    );

    $resultadoId = $consultaId->fetch();
    $novoId = (int) $resultadoId['NOVO_ID'];

    $sql = 'INSERT INTO EMAIL
            (ID, PESSOA_ID, ENDERECO_EMAIL)
            VALUES (:id, :pessoa_id, :email)';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $novoId, PDO::PARAM_INT);
    $stmt->bindParam(':pessoa_id', $pessoaId, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        'mensagem' => 'Email criado com sucesso',
        'ID' => $novoId,
        'PESSOA_ID' => (int) $pessoaId,
        'ENDERECO_EMAIL' => $email
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao criar email',
        'detalhe' => $e->getMessage()
    ]);
}