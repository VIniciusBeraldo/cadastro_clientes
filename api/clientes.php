<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json'); // Informa ao navegador que a resposta é JSON
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem (útil para desenvolvimento)

// 1. Configurações de Conexão com o Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

// 2. Conectar ao Banco de Dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    // Retorna um erro JSON em caso de falha na conexão
    echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
    exit(); // Para a execução do script
}

// Verifica se um ID foi passado na URL para buscar um único cliente
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
        echo json_encode($client); // Retorna o objeto do cliente
    } else {
        echo json_encode(['error' => 'Cliente não encontrado.']);
    }
    $stmt->close();

} else {
    // Caso contrário, retorna todos os clientes (comportamento original)
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
                nome_completo ASC";
    $result = $conn->query($sql);

    $clientes = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
    echo json_encode($clientes); // Retorna um array de clientes
}

$conn->close();
?>