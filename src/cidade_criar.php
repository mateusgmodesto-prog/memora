<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$cidade = isset($dados['cidade']) ? trim($dados['cidade']) : null;
$estado = isset($dados['estado']) ? strtoupper(trim($dados['estado'])) : null;

if (empty($cidade) || empty($estado)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Cidade e estado sao obrigatorios.']);
    exit;
}

if (strlen($estado) !== 2) {
    http_response_code(400);
    echo json_encode(['erro' => 'O estado deve possuir duas letras.']);
    exit;
}

try {
    $pdo = conectar_banco();
    $pdo->beginTransaction();

    $consultaId = $pdo->query(
        'SELECT COALESCE(MAX(ID_CIDADE), 0) + 1 AS NOVO_ID FROM CIDADE'
    );

    $resultadoId = $consultaId->fetch();
    $novoId = (int) $resultadoId['NOVO_ID'];

    $sql = 'INSERT INTO CIDADE
            (ID_CIDADE, CIDADE, ESTADO)
            VALUES (:id_cidade, :cidade, :estado)';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_cidade', $novoId, PDO::PARAM_INT);
    $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
    $stmt->execute();

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        'mensagem' => 'Cidade criada com sucesso',
        'ID_CIDADE' => $novoId,
        'CIDADE' => $cidade,
        'ESTADO' => $estado
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao criar cidade',
        'detalhe' => $e->getMessage()
    ]);
}