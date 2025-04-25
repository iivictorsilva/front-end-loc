<?php
// login.php - Autenticação de usuários

// Inicia a sessão
session_start();

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
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validações básicas
if (empty($email) || empty($senha)) {
    die("<script>alert('E-mail e senha são obrigatórios!'); window.history.back();</script>");
}

// Busca o usuário no banco de dados
$sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    
    // Verifica a senha
    if (password_verify($senha, $usuario['senha'])) {
        // Autenticação bem-sucedida
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['logado'] = true;
        
        // Redireciona imediatamente para o dashboard
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "<script>alert('Senha incorreta!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Usuário não encontrado!'); window.history.back();</script>";
}

$stmt->close();
$conexao->close();
?>