<?php
// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// 1. Configurações de Conexão com o Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_clientes";

// 2. Conectar ao Banco de Dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// 3. Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 4. Obter e Sanitizar os dados do formulário
    $nomeCompleto = $_POST['nomeCompleto'] ?? '';
    $dataNascimento = $_POST['dataNascimento'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $endereco = $_POST['endereco'] ?? '';

    // Validação básica
    if (empty($nomeCompleto) || empty($dataNascimento) || empty($cpf) || empty($email) || empty($endereco)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
        $conn->close();
        exit();
    }

    // 5. Verificar se o CPF já existe
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

    // 6. Verificar se o E-mail já existe
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

    // 7. Preparar e Executar a Inserção (usando Prepared Statements para segurança)
    $sql = "INSERT INTO clientes (nome_completo, data_nascimento, cpf, telefone, email, endereco, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    // 'ssssss' indica que todos os parâmetros são strings
    $stmt->bind_param("ssssss", $nomeCompleto, $dataNascimento, $cpf, $telefone, $email, $endereco);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cliente cadastrado com sucesso!']);
    } else {
        // Erro na execução da consulta
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar cliente: ' . $stmt->error]);
    }

    // Fechar o statement
    $stmt->close();

} else {
    // Se a requisição não for POST
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}

// Fechar a conexão com o banco de dados
$conn->close();
?>