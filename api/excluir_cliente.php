<?php
// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

// Conecta ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Verifica se a requisição é POST e se o ID foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Converte o ID para um número inteiro por segurança

    // Prepara a consulta SQL para excluir o cliente. Usar Prepared Statements é CRUCIAL para segurança!
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' indica que o parâmetro é um inteiro

    // Executa a consulta
    if ($stmt->execute()) {
        // Verifica se alguma linha foi afetada (se o cliente realmente existia e foi excluído)
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado ou já excluído.']);
        }
    } else {
        // Erro na execução da consulta
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir cliente: ' . $stmt->error]);
    }

    // Fecha o statement
    $stmt->close();
} else {
    // Caso a requisição não seja POST ou o ID não foi fornecido
    echo json_encode(['success' => false, 'message' => 'Requisição inválida. O ID do cliente não foi fornecido.']);
}

// Fecha a conexão com o banco de dados
$conn->close();
?>