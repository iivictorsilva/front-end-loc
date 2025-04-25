<?php
// Inicia a sessão e verifica se o usuário está logado
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
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

// Verifica erros na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Busca os dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql_usuario);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LocCar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Estilos consistentes com a página de login - AGORA EM AZUL */
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logout-btn {
            background: none;
            border: 1px solid white;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: white;
            color: #1E3A8A;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .page-title {
            color: #1E3A8A;
            margin-bottom: 30px;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .car-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .car-card:hover {
            transform: translateY(-5px);
        }

        .car-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .car-info {
            padding: 20px;
        }

        .car-model {
            font-size: 20px;
            margin: 0 0 10px;
            color: #333;
        }

        .car-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }

        .car-price {
            font-size: 18px;
            font-weight: bold;
            color: #1E3A8A;
            margin-bottom: 15px;
        }

        .car-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-available {
            background-color: #e6f7e6;
            color: #2ecc71;
        }

        .status-unavailable {
            background-color: #ffebeb;
            color: #e74c3c;
        }

        .rent-btn {
            width: 100%;
            padding: 10px;
            background-color: #1E3A8A;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .rent-btn:hover {
            background-color: #142d6b;
        }

        .rent-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 15px;
            border: 1px solid #1E3A8A;
            background: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover, .filter-btn.active {
            background-color: #1E3A8A;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">LocCar</div>
            <div class="user-info">
                <span>Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
                
                <button class="logout-btn">Sair</button>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Carros Disponíveis para Aluguel</h1>
        
        <div class="filter-section">
            <button class="filter-btn active">Todos</button>
            <button class="filter-btn">Economicos</button>
            <button class="filter-btn">Intermediários</button>
            <button class="filter-btn">Luxo</button>
            <button class="filter-btn">SUVs</button>
        </div>

        <div class="cars-grid">
            <!-- Card 1 - Econômico -->
            <div class="car-card" data-category="economicos">
                <img src="./assents/img/golf.jpeg" alt="Volkswagen Golf" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">Volkswagen Golf</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2020</span>
                        <span><i class="fas fa-gas-pump"></i> Flex</span>
                        <span><i class="fas fa-cogs"></i> Automático</span>
                    </div>
                    <div class="car-price">R$ 180,00 /dia</div>
                    <span class="car-status status-available">Disponível</span>
                    <button class="rent-btn">Alugar agora</button>
                </div>
            </div>

            <!-- Card 2 - Intermediário -->
            <div class="car-card" data-category="intermediarios">
                <img src="./assents/img/corolla.jpeg" alt="Toyota Corolla" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">Toyota Corolla</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2021</span>
                        <span><i class="fas fa-gas-pump"></i> Gasolina</span>
                        <span><i class="fas fa-cogs"></i> Automático</span>
                    </div>
                    <div class="car-price">R$ 220,00 /dia</div>
                    <span class="car-status status-available">Disponível</span>
                    <button class="rent-btn">Alugar agora</button>
                </div>
            </div>

            <!-- Card 3 - SUV -->
            <div class="car-card" data-category="suvs">
                <img src="./assents/img/jeep.jpeg" alt="Jeep Renegade" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">Jeep Renegade</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2022</span>
                        <span><i class="fas fa-gas-pump"></i> Diesel</span>
                        <span><i class="fas fa-cogs"></i> Automático</span>
                    </div>
                    <div class="car-price">R$ 280,00 /dia</div>
                    <span class="car-status status-available">Disponível</span>
                    <button class="rent-btn">Alugar agora</button>
                </div>
            </div>

            <!-- Card 4 - Luxo -->
            <div class="car-card" data-category="luxo">
                <img src="./assents/img/bmw.jpeg" alt="BMW 320i" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">BMW 320i</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2021</span>
                        <span><i class="fas fa-gas-pump"></i> Gasolina</span>
                        <span><i class="fas fa-cogs"></i> Automático</span>
                    </div>
                    <div class="car-price">R$ 350,00 /dia</div>
                    <span class="car-status status-unavailable">Indisponível</span>
                    <button class="rent-btn" disabled>Indisponível</button>
                </div>
            </div>

            <!-- Card 5 - Econômico -->
            <div class="car-card" data-category="economicos">
                <img src="./assents/img/fiat.jpeg" alt="Fiat Argo" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">Fiat Argo</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2022</span>
                        <span><i class="fas fa-gas-pump"></i> Flex</span>
                        <span><i class="fas fa-cogs"></i> Manual</span>
                    </div>
                    <div class="car-price">R$ 150,00 /dia</div>
                    <span class="car-status status-available">Disponível</span>
                    <button class="rent-btn">Alugar agora</button>
                </div>
            </div>

            <!-- Card 6 - Intermediário -->
            <div class="car-card" data-category="intermediarios">
                <img src="./assents/img/civic.webp" alt="Honda Civic" class="car-image">
                <div class="car-info">
                    <h3 class="car-model">Honda Civic</h3>
                    <div class="car-details">
                        <span><i class="fas fa-car"></i> 2022</span>
                        <span><i class="fas fa-gas-pump"></i> Gasolina</span>
                        <span><i class="fas fa-cogs"></i> Automático</span>
                    </div>
                    <div class="car-price">R$ 250,00 /dia</div>
                    <span class="car-status status-available">Disponível</span>
                    <button class="rent-btn">Alugar agora</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos da interface
        const filterBtns = document.querySelectorAll('.filter-btn');
        const logoutBtn = document.querySelector('.logout-btn');
        const rentBtns = document.querySelectorAll('.rent-btn:not(:disabled)');
        
        // Mapeamento de filtros para categorias
        const filterMap = {
            'todos': 'todos',
            'economicos': 'economicos',
            'intermediários': 'intermediarios',
            'luxo': 'luxo',
            'suvs': 'suvs'
        };

        // Filtros de carros
        function setupFilters() {
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove classe active de todos os botões
                    filterBtns.forEach(b => b.classList.remove('active'));
                    
                    // Adiciona classe active ao botão clicado
                    btn.classList.add('active');
                    
                    // Obtém o tipo de filtro
                    const filterText = btn.textContent.trim().toLowerCase();
                    const filterType = filterMap[filterText];
                    
                    // Filtra os carros
                    filterCars(filterType);
                });
            });
        }

        // Função para filtrar carros
        function filterCars(filterType) {
            const allCars = document.querySelectorAll('.car-card');
            
            allCars.forEach(car => {
                if (filterType === 'todos') {
                    car.style.display = 'block';
                } else {
                    const carCategory = car.getAttribute('data-category');
                    car.style.display = carCategory === filterType ? 'block' : 'none';
                }
            });
        }

        // Logout - Redireciona para cadastro
        function setupLogout() {
            logoutBtn.addEventListener('click', () => {
                if(confirm('Deseja realmente sair?')) {
                    fetch('logout.php', {
                    method: 'POST'
            })
            .then(response => {
                if(response.ok) {
                        window.location.href = 'index.html#signUp';
                    } else {
                        throw new Error('Falha no logout');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    window.location.href = 'index.html#signUp';
                });
            }
        });
    }

        // Sistema de aluguel
        function setupRentals() {
            rentBtns.forEach(btn => {
                btn.addEventListener('click', async function() {
                    const carCard = this.closest('.car-card');
                    const carModel = carCard.querySelector('.car-model').textContent;
                    const carId = carCard.dataset.carId;
                    
                    if(!confirm(`Deseja alugar o ${carModel}?`)) return;
                    
                    try {
                        const response = await fetch('phpLogin/alugar.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ 
                                carId: carId,
                                userId: <?php echo $_SESSION['usuario_id']; ?>
                            })
                        });
                        
                        const data = await response.json();
                        
                        if(!response.ok) throw new Error(data.message || 'Erro desconhecido');
                        
                        alert(`Aluguel do ${carModel} confirmado!`);
                        updateCarStatus(carCard, false);
                    } catch (error) {
                        alert(`Falha no aluguel: ${error.message}`);
                        console.error('Erro:', error);
                    }
                });
            });
        }

        // Atualiza status do carro após aluguel
        function updateCarStatus(carElement, isAvailable) {
            const statusElement = carElement.querySelector('.car-status');
            const buttonElement = carElement.querySelector('.rent-btn');
            
            if (isAvailable) {
                statusElement.textContent = 'Disponível';
                statusElement.className = 'car-status status-available';
                buttonElement.disabled = false;
                buttonElement.textContent = 'Alugar agora';
            } else {
                statusElement.textContent = 'Indisponível';
                statusElement.className = 'car-status status-unavailable';
                buttonElement.disabled = true;
                buttonElement.textContent = 'Indisponível';
            }
        }

        // Inicializa todas as funcionalidades
        function init() {
            setupFilters();
            setupLogout();
            setupRentals();
            
            // Filtra inicialmente por 'todos'
            if (filterBtns.length > 0) {
                filterBtns[0].click();
            }
        }

        // Inicia o dashboard
        init();
    });
    </script>
</body>
</html>