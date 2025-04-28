<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id'])) {
    $_SESSION['selected_car'] = [
        'id' => (int)$_POST['car_id'],
        'model' => htmlspecialchars($_POST['car_model']),
        'price' => htmlspecialchars($_POST['car_price']),
        'image' => htmlspecialchars($_POST['car_image'])
    ];
    header("Location: alugar.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>