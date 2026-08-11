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

$idPessoa = $dados['id_pessoa'] ?? null;
$idCidade = $dados['id_cidade'] ?? null;
$cep = isset($dados['cep']) ? trim($dados['cep']) : null;
$rua = isset($dados['rua']) ? trim($dados['rua']) : null;
$bairro = isset($dados['bairro']) ? trim($dados['bairro']) : null;
$numero = isset($dados['numero']) ? trim($dados['numero']) : null;

if (
    empty($id) ||
    empty($idPessoa) ||
    empty($idCidade) ||
    empty($cep) ||
    empty($rua) ||
    empty($bairro) ||
    empty($numero)
) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID e todos os campos sao obrigatorios.']);
    exit;
}

try {
    $pdo = conectar_banco();

    $sql = 'UPDATE ENDERECO SET
                ID_PESSOA = :id_pessoa,
                ID_CIDADE = :id_cidade,
                CEP = :cep,
                RUA = :rua,
                BAIRRO = :bairro,
                NUMERO = :numero
            WHERE ID_ENDERECO = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stmt->bindParam(':id_cidade', $idCidade, PDO::PARAM_INT);
    $stmt->bindParam(':cep', $cep, PDO::PARAM_STR);
    $stmt->bindParam(':rua', $rua, PDO::PARAM_STR);
    $stmt->bindParam(':bairro', $bairro, PDO::PARAM_STR);
    $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Endereco nao encontrado']);
        exit;
    }

    echo json_encode([
        'mensagem' => 'Endereco atualizado com sucesso',
        'ID_ENDERECO' => (int) $id,
        'ID_PESSOA' => (int) $idPessoa,
        'ID_CIDADE' => (int) $idCidade,
        'CEP' => $cep,
        'RUA' => $rua,
        'BAIRRO' => $bairro,
        'NUMERO' => $numero
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao atualizar endereco',
        'detalhe' => $e->getMessage()
    ]);
}
