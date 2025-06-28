<?php

/**
 * Arquivo: api/clientes.php
 * Descrição: Script API para gerenciar (listar, buscar por ID) clientes no banco de dados.
 * Autor: Vinicius Beraldo da Silva
 * Data: 27/06/2025
 *
 * Este script lida com requisições GET para retornar:
 * - Todos os clientes (se nenhum ID for fornecido)
 * - Um cliente específico (se um ID for fornecido via GET)
 */

header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *'); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Validando se chamada foi feita pelo botão excluir
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT
                id,
                nome_completo,
                DATE_FORMAT(data_nascimento, '%Y-%m-%d') AS data_nascimento,
                cpf,
                telefone,
                email,
                endereco,
                DATE_FORMAT(data_cadastro, '%d/%m/%Y %H:%i:%s') AS data_cadastro
            FROM
                clientes
            WHERE
                id = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
        echo json_encode($client); 
    } else {
        echo json_encode(['error' => 'Cliente não encontrado.']);
    }
    $stmt->close();

} else {
    // Caso não for chamado pelo botão excluir, vai alimentar toda a lista com os clientes encontrados.
    $sql = "SELECT
                id,
                nome_completo,
                DATE_FORMAT(data_nascimento, '%d/%m/%Y') AS data_nascimento,
                cpf,
                telefone,
                email,
                endereco,
                DATE_FORMAT(data_cadastro, '%d/%m/%Y %H:%i:%s') AS data_cadastro
            FROM
                clientes
            ORDER BY
                id ASC";
    $result = $conn->query($sql);

    $clientes = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
    echo json_encode($clientes);
}

$conn->close();
?>