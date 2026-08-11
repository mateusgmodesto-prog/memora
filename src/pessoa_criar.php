<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$dadosJson = json_decode(file_get_contents('php://input'), true);
$nome = $_POST['nome'] ?? ($dadosJson['nome'] ?? null);
$nome = is_string($nome) ? trim($nome) : null;

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e obrigatorio']);
    exit;
}

try {
    $pdo = conectar_banco();
    $pdo->beginTransaction();

    $consultaId = $pdo->query(
        'SELECT COALESCE(MAX(ID), 0) + 1 AS NOVO_ID FROM PESSOA'
    );

    $resultadoId = $consultaId->fetch();
    $novoId = (int) $resultadoId['NOVO_ID'];

    $sql = 'INSERT INTO PESSOA (ID, NOME) VALUES (:id, :nome)';
    $stm = $pdo->prepare($sql);
    $stm->bindParam(':id', $novoId, PDO::PARAM_INT);
    $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stm->execute();

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        'mensagem' => 'Pessoa criada com sucesso',
        'ID' => $novoId,
        'NOME' => $nome
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao criar pessoa',
        'detalhe' => $e->getMessage()
    ]);
}