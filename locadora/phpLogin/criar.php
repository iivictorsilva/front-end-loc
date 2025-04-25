<?php
// criar.php - Cadastro de usuários

// Configurações do banco de dados
$host = "localhost";
$usuario = "root"; // Substitua pelo seu usuário do MySQL
$senha = ""; // Substitua pela sua senha do MySQL
$banco = "loc_car";

// Conexão com o banco de dados
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica erros na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Recebe os dados do formulário
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validações básicas
if (empty($nome) || empty($email) || empty($senha)) {
    die("<script>alert('Todos os campos são obrigatórios!'); window.history.back();</script>");
}

if (strlen($senha) < 6) {
    die("<script>alert('A senha deve ter pelo menos 6 caracteres!'); window.history.back();</script>");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<script>alert('E-mail inválido!'); window.history.back();</script>");
}

// Verifica se o e-mail já está cadastrado
$sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql_verifica);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>");
}
$stmt->close();

// Hash da senha (nunca armazene senhas em texto puro)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Prepara e executa a inserção
$sql_insere = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql_insere);
$stmt->bind_param("sss", $nome, $email, $senha_hash);

if ($stmt->execute()) {
    // Mensagem de sucesso com pop-up e redirecionamento
    echo "<script>
            alert('Cadastro realizado com sucesso!');
            window.location.href = '../index.html';
          </script>";
} else {
    echo "<script>alert('Erro ao cadastrar: " . addslashes($conexao->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conexao->close();
?>