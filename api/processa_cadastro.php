<?php

/**
 * Arquivo: api/processa_cadastro.php
 * Descrição: Script API para cadastrar novos clientes na tabela cliente.
 * Autor: Vinicius Beraldo da Silva
 * 
 * Este script lida com requisições POST para:
 * - Armazenar todos os dados informados no form.
 * - Valida caso o CPF ou Email já exista no banco.
 * - Realiza o INSERT na tabela de clientes com os dados armazenados do formulario.
 */

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nomeCompleto   = $_POST['nomeCompleto'] ?? '';
    $dataNascimento = $_POST['dataNascimento'] ?? '';
    $cpf            = $_POST['cpf'] ?? '';
    $telefone       = $_POST['telefone'] ?? '';
    $email          = $_POST['email'] ?? '';
    $endereco       = $_POST['endereco'] ?? '';


    // -- Validando se o cpf já existe no banco -- //
    $stmt_cpf = $conn->prepare("SELECT id FROM clientes WHERE cpf = ?");
    $stmt_cpf->bind_param("s", $cpf);
    $stmt_cpf->execute();
    $stmt_cpf->store_result();

    if ($stmt_cpf->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Já existe um cliente cadastrado com este CPF.']);
        $stmt_cpf->close();
        $conn->close();
        exit();
    }

    $stmt_cpf->close();
    // -- Fim da validação do cpf -- //

    // -- Validando se o email já existe no banco -- //
    $stmt_email = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();
    $stmt_email->store_result(); 

    if ($stmt_email->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Já existe um cliente cadastrado com este E-mail.']);
        $stmt_email->close();
        $conn->close();
        exit();
    }
    $stmt_email->close();
    // -- Fim da validação do email -- //

    $sql = "INSERT INTO clientes (nome_completo, data_nascimento, cpf, telefone, email, endereco, data_cadastro) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssss", $nomeCompleto, $dataNascimento, $cpf, $telefone, $email, $endereco);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cliente cadastrado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar cliente: ' . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}

$conn->close();
?>