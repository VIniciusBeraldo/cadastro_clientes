<?php

/**
 * Arquivo: api/excluir_cliente.php
 * Descrição: Script API para excluir um cliente no banco.
 * Autor: Vinicius Beraldo da Silva
 * 
 * Este script lida com requisições POST para:
 * - Realizar um DELETE no banco através do id passado ao executar a function deletarCliente
 *   a mesma é chamada ao clicar no botão excluir na tabela de cliente, onde o mesmo contem o id no dataset.
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

// Validando se a requisão foi devidamente atendida e iniciando a exclusão.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id); 

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado ou já excluído.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir cliente: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida. O ID do cliente não foi fornecido.']);
}

$conn->close();
?>