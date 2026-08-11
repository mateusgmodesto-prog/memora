<?php

require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido. Use POST.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$idPessoa = $dados['id_pessoa'] ?? null;
$idCidade = $dados['id_cidade'] ?? null;
$cep = isset($dados['cep']) ? trim($dados['cep']) : null;
$rua = isset($dados['rua']) ? trim($dados['rua']) : null;
$bairro = isset($dados['bairro']) ? trim($dados['bairro']) : null;
$numero = isset($dados['numero']) ? trim($dados['numero']) : null;

if (
    empty($idPessoa) ||
    empty($idCidade) ||
    empty($cep) ||
    empty($rua) ||
    empty($bairro) ||
    empty($numero) 
) {
    http_response_code(400);
    echo json_encode(['erro' => 'Todos os campos sao obrigatorios.']);
    exit;
}

try {
    $pdo = conectar_banco();
    $pdo->beginTransaction();

    $consultaId = $pdo->query(
        'SELECT COALESCE(MAX(ID_ENDERECO), 0) + 1 AS NOVO_ID FROM ENDERECO'
    );

    $resultadoId = $consultaId->fetch();
    $novoId = (int) $resultadoId['NOVO_ID'];

    $sql = 'INSERT INTO ENDERECO
            (ID_ENDERECO, ID_PESSOA, ID_CIDADE, CEP, RUA, BAIRRO, NUMERO)
            VALUES
            (:id_endereco, :id_pessoa, :id_cidade, :cep, :rua, :bairro, :numero)';
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_endereco', $novoId, PDO::PARAM_INT);
    $stmt->bindParam(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stmt->bindParam(':id_cidade', $idCidade, PDO::PARAM_INT);
    $stmt->bindParam(':cep', $cep, PDO::PARAM_STR);
    $stmt->bindParam(':rua', $rua, PDO::PARAM_STR);
    $stmt->bindParam(':bairro', $bairro, PDO::PARAM_STR);
    $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
    $stmt->execute();

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        'mensagem' => 'Endereco criado com sucesso',
        'ID_ENDERECO' => $novoId,
        'ID_PESSOA' => (int) $idPessoa,
        'ID_CIDADE' => (int) $idCidade,
        'CEP' => $cep,
        'RUA' => $rua,
        'BAIRRO' => $bairro,
        'NUMERO' => $numero
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao criar endereco',
        'detalhe' => $e->getMessage()
    ]);
}