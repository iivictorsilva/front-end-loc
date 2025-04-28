<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carData'])) {
    $carData = json_decode($_POST['carData'], true);
    
    if ($carData && json_last_error() === JSON_ERROR_NONE) {
        $_SESSION['selected_car'] = $carData;
        echo "Dados do carro armazenados com sucesso!";
    } else {
        http_response_code(400);
        echo "Erro ao decodificar dados do carro";
    }
} else {
    http_response_code(405);
    echo "Método não permitido";
}
exit();
?>