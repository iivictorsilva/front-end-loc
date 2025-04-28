<?php
session_start();

// Verifica se o usuário está logado e tem um carro selecionado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['selected_car'])) {
    header("Location: index.html");
    exit();
}

// Configurações do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "loc_car";

// Conexão com o banco de dados
$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Processar formulário de aluguel
$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_inicio'])) {
    // Validar e sanitizar dados
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $carro_id = (int)$_POST['carro_id'];

    // Verificar se todos os campos estão preenchidos
    if (empty($data_inicio) || empty($data_fim) || empty($forma_pagamento) || $carro_id <= 0) {
        $erro = "Por favor, preencha todos os campos corretamente.";
    } else {
        // Inserir aluguel no banco de dados
        $sql = "INSERT INTO alugueis (usuario_id, carro_id, data_inicio, data_fim, forma_pagamento, status) 
                VALUES (?, ?, ?, ?, ?, 'confirmado')";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iisss", $_SESSION['usuario_id'], $carro_id, $data_inicio, $data_fim, $forma_pagamento);
        
        if ($stmt->execute()) {
            // Atualizar status do carro para indisponível
            $sql_update = "UPDATE carros SET disponivel = 0 WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("i", $carro_id);
            $stmt_update->execute();
            $stmt_update->close();
            
            $_SESSION['aluguel_sucesso'] = true;
            header("Location: aluguel_confirmado.php");
            exit();
        } else {
            $erro = "Erro ao processar o aluguel. Por favor, tente novamente.";
        }
        $stmt->close();
    }
}

// Buscar dados do usuário
$sql_usuario = "SELECT nome, email, cpf, telefone, endereco FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql_usuario);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

$carro = $_SESSION['selected_car'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Aluguel - LocCar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #1E3A8A;
            --secondary-color: #142d6b;
            --error-color: #e74c3c;
            --text-color: #333;
            --light-gray: #f6f5f7;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .title {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }
        
        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .car-model {
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .car-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }
        
        .car-price {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 20px 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .error {
            color: var(--error-color);
            background-color: #ffebeb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .user-info-item {
            margin-bottom: 12px;
        }
        
        .info-label {
            font-weight: bold;
            display: block;
        }
        
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Seção do Veículo -->
        <div class="card">
            <h2 class="title">Resumo do Veículo</h2>
            <img src="<?= htmlspecialchars($carro['image']) ?>" alt="<?= htmlspecialchars($carro['model']) ?>" class="car-image">
            <h3 class="car-model"><?= htmlspecialchars($carro['model']) ?></h3>
            <div class="car-details">
                <span><i class="fas fa-car"></i> Ano: 2020</span>
                <span><i class="fas fa-gas-pump"></i> Combustível: Flex</span>
                <span><i class="fas fa-cogs"></i> Câmbio: Automático</span>
            </div>
            <div class="car-price">R$ 180,00 /dia</div>
            
            <div class="user-info">
                <h3 class="title">Seus Dados</h3>
                <div class="user-info-item">
                    <span class="info-label">Nome:</span>
                    <span><?= htmlspecialchars($usuario['nome']) ?></span>
                </div>
                <div class="user-info-item">
                    <span class="info-label">Email:</span>
                    <span><?= htmlspecialchars($usuario['email']) ?></span>
                </div>
                <!-- Outros campos do usuário -->
            </div>
        </div>
        
        <!-- Seção do Formulário -->
        <div class="card">
            <h2 class="title">Informações do Aluguel</h2>
            
            <?php if (isset($erro)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> <?= $erro ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="carro_id" value="<?= $carro['id'] ?>">
                
                <div class="form-group">
                    <label for="data_inicio"><i class="far fa-calendar-alt"></i> Data de Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" required 
                           value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label for="data_fim"><i class="far fa-calendar-alt"></i> Data de Término</label>
                    <input type="date" id="data_fim" name="data_fim" required 
                           min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label for="forma_pagamento"><i class="fas fa-credit-card"></i> Forma de Pagamento</label>
                    <select id="forma_pagamento" name="forma_pagamento" required>
                        <option value="">Selecione...</option>
                        <option value="cartao_credito">Cartão de Crédito</option>
                        <option value="cartao_debito">Cartão de Débito</option>
                        <option value="pix">PIX</option>
                        <option value="boleto">Boleto Bancário</option>
                    </select>
                </div>
                
                <a href="aluguel_confirmação.php"><button type="submit" class="btn">
                    <i class="fas fa-check"></i> Confirmar Aluguel
                </button></a>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataInicio = document.getElementById('data_inicio');
        const dataFim = document.getElementById('data_fim');
        
        dataInicio.addEventListener('change', function() {
            dataFim.min = this.value;
            if (dataFim.value && new Date(dataFim.value) < new Date(this.value)) {
                alert('A data de término deve ser posterior à data de início');
                dataFim.value = '';
            }
        });
        
        dataFim.addEventListener('change', function() {
            if (dataInicio.value && new Date(this.value) < new Date(dataInicio.value)) {
                alert('A data de término deve ser posterior à data de início');
                this.value = '';
            }
        });
    });
    </script>
</body>
</html>