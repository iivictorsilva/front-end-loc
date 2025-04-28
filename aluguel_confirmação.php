<?php
session_start();

if (!isset($_SESSION['logado']) || !isset($_SESSION['aluguel_sucesso'])) {
    header("Location: dashboard.php");
    exit();
}

$carro = $_SESSION['selected_car'];
unset($_SESSION['aluguel_sucesso']);
unset($_SESSION['selected_car']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aluguel Confirmado - LocCar</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f5f7;
            color: #333;
        }

        header {
            background-color: #1E3A8A;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 50px auto;
            text-align: center;
        }

        .confirmation-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .check-icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
        }

        .confirmation-title {
            font-size: 28px;
            color: #1E3A8A;
            margin-bottom: 20px;
        }

        .confirmation-message {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .car-model {
            font-weight: bold;
            color: #1E3A8A;
        }

        .btn {
            padding: 12px 30px;
            background-color: #1E3A8A;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #142d6b;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <div class="check-icon"><i class="fas fa-check-circle"></i></div>
        <h1>Aluguel Confirmado!</h1>
        <p>O veículo <strong><?= htmlspecialchars($carro['model']) ?></strong> foi alugado com sucesso.</p>
        <a href="dashboard.php" class="btn">Voltar ao Dashboard</a>
    </div>
</body>
</html>


