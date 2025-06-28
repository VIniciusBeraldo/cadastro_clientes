<?php

/**
 * Arquivo: api/atualizar_cliente.php
 * Descrição: Script API para atualizar os dados do clientes no banco quando enviado atraves do editar clientes.
 * Autor: Vinicius Beraldo da Silva
 * 
 * Este script lida com requisições POST para:
 * - Realizar um update na tabela de clientes, atualizando com os dados recebidos atraves da requisição 
 *   ao clicar em Salvar alterações na tela Editar Clientes.
 */

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Verifica se a requisição é POST e se todos os campos necessários foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomeCompleto'], $_POST['dataNascimento'], $_POST['cpf'], $_POST['telefone'], $_POST['email'], $_POST['endereco'])) {

    $id = intval($_POST['id']);
    $nome_completo = $_POST['nomeCompleto'];
    $data_nascimento = $_POST['dataNascimento'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];

    $sql = "UPDATE clientes SET
                nome_completo = ?,
                data_nascimento = ?,
                cpf = ?,
                telefone = ?,
                email = ?,
                endereco = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssssi", $nome_completo, $data_nascimento, $cpf, $telefone, $email, $endereco, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Cliente atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma alteração detectada ou cliente não encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar cliente: ' . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida ou dados incompletos.']);
}

$conn->close();
?>