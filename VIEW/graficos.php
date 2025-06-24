<?php
require_once('../MODEL/model_usuario.php');
require_once('../CONTROLLER/controller_historico.php');

session_start();
// Verificar se o logout foi solicitado
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Destruir todas as variáveis de sessão
    $_SESSION = array();
    
    // Se desejar destruir a sessão completamente, apague também o cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Finalmente, destruir a sessão
    session_destroy();
    
    // Redirecionar para a página de login
    header("Location: ../VIEW/index.php");
    exit;
}

if(isset($_GET["id"])){
    $id = $_GET["id"]; 

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioId($id);

    if (!empty($usuario) && isset($usuario[0])) {
        $usu = $usuario[0];
    } else {
        $usu = [
            "FOTO_PERFIL" => ""
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Gráficos</title>
    <link rel="stylesheet" href="../STYLES/graficos.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .logout-btn {
            color: #007701;
            font-size: 1.5rem;
            margin-left: 15px;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            color: #009903;
            transform: scale(1.1);
        }
        .header.scrolled .logout-btn i{
            color: #ffffff !important; 
        }
        .header.scrolled .logout-btn i:hover {
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <header>
            <a href="<?php echo './index.php?id=' . $id; ?>"><img class="img" src="../IMAGES/LOGO2.png" alt=""></a>
            <div class="campos">
                <ul>
                    <li><a href="<?php echo './index.php?id=' . $id; ?>">Início</a></li>
                    <li><a href="<?php echo './galeria.php?id=' . $id; ?>">Galeria</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="<?php echo './mapas.php?id=' . $id; ?>">Mapas</a></li>
                    <li><a href="<?php echo './graficos.php?id=' . $id; ?>">Gráficos</a></li>
                    <li><a href="<?php echo './contato.php?id=' . $id; ?>">Contato</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo './sobre-nos.php?id=' . $id; ?>">Sobre Nós</a></li>
                </ul>
            </div>
            <?php if(!empty($id)): ?>
            <div class="perfil">
                <a href="<?php echo './config.php?id=' . $id; ?>">
                    <img id="preview-img-profile" src="../IMAGES/PERFIL/<?php echo !empty($usu["FOTO_PERFIL"]) ? $usu["FOTO_PERFIL"] : 'PERFIL_SEM_FOTO.png'; ?>">
                </a>
            </div>
            <?php endif; ?>
            <a href="?logout=1" class="logout-btn" title="Sair">
                <i class='bx bx-log-out'></i>
            </a>
        </header>
    </div>

    <div class="verde"></div>

    <div class="main">
        <main>
            <section id="graficos"> 
                <div class="text">
                    <h2>Gráficos</h2>
                    <p>Visualização de dados coletados pelos sensores em diferentes dimensões temporais e geográficas. Os gráficos abaixo apresentam análises comparativas e tendências dos parâmetros monitorados</p>
                </div>

                <div class="grafico-boxes">
                    <!-- Gráfico 1 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Tipo de Sensor por Dados</div>
                            <div class="grafico-container">
                                <canvas id="graficoColunas"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 2 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Dados por Data de Coleta</div>
                            <div class="grafico-container">
                                <canvas id="graficoLinha"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grafico-boxes">
                    <!-- Gráfico 3 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Dados por Hora de Coleta</div>
                            <div class="grafico-container">
                                <canvas id="graficoHora"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 4 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Média por Unidade de Medida</div>
                            <div class="grafico-container">
                                <canvas id="graficoMediaUnidade"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grafico-boxes">
                    <!-- Gráfico 5 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Latitude vs Dados</div>
                            <div class="grafico-container">
                                <canvas id="graficoLat"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 6 -->
                    <div class="grafico-wrapper">
                        <div class="grafico-card">
                            <div class="grafico-title">Longitude vs Dados</div>
                            <div class="grafico-container">
                                <canvas id="graficoLong"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão Editar como Admin -->
                <?php if(isset($usu['TIPO_USUARIO']) && $usu['TIPO_USUARIO'] === 'ADMINISTRADOR'): ?>
                    <div class="admin-button-container">
                        <a href="./gerenciar_sensores.php?id=<?php echo !empty($id) ? '?id='.$id : ''; ?>" class="admin-button" style="text-decoration: none;">
                            <i class="fas fa-cog"></i> Editar como Admin
                        </a>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <div class="conteudo2">
        <div class="bloco">
            <i class='bx bxs-phone'></i>
            <div class="texto">
                <p style="font-weight: bold;">Ligue para nós</p>
                <p>+55 (19) 9935-7890</p>
            </div>
        </div>
        <div class="bloco">
            <i class='bx bxs-envelope' ></i>
            <div class="texto">
                <p style="font-weight: bold;">Envie um e-mail</p>
                <p>safezone@gmail.com</p>
            </div>
        </div>
        <div class="bloco">
            <i class='bx bxs-time' ></i>
            <div class="texto">
                <p style="font-weight: bold;">Seg.-Sáb.:</p>
                <p>08h00 – 18h00</p>
            </div>
        </div>
        <div class="bloco">
            <i class="fa-solid fa-location-dot"></i>
            <div class="texto">
                <p style="font-weight: bold;">Venha nos visitar:</p>
                <p>Rua Safe Zone, 123 – <br> Centro, Tambaú</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <footer>
            <div class="sobre">
                <div class="texts">
                    <h4>Sobre Nós</h4>
                </div>
                <p>O Safe Zone é um espaço seguro dedicado a fornecer suporte, informações e recursos para aqueles que precisam de ajuda. Nossa missão é criar uma comunidade acolhedora e acessível para todos.</p>
                <div class="redes">
                    <i class='bx bxl-facebook-square'></i>
                    <i class='bx bxl-instagram' ></i>
                    <i class='bx bxl-whatsapp' ></i>
                    <i class='bx bxl-twitter' ></i>
                    <i class='bx bxl-github' ></i>
                </div>
            </div>
            <div class="links">
                <h4>Links Úteis</h4>
                <ul>
                    <li><a href="#">Quem Somos</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Políticas de Privacidade</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">Perguntas Frequentes</a></li>
                </ul>
            </div>
            <div class="not-empresa">
                <h4>Notícias da Empresa</h4>
                <h4>Grande Evento</h4>
                <p>Terça , 24 junho 2025</p>
                <p>Após muito esforço, chegou a hora! Convidamos você para a apresentação do nosso Trabalho de Conclusão de Curso (TCC).</p>
            </div>
        </footer>
    </div>

    <div class="rodape">
        <p>Safe Zone &copy; 2025 - Todos os Direitos Reservados</p>
    </div>

    <button class="back-to-top" title="Voltar ao topo">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script src="../SCRIPTS/graficos.js"></script>
    <script src="../SCRIPTS/jquery.min.js"></script>
    <script src="../SCRIPTS/jquery.backtotop.js"></script>
    <script src="../SCRIPTS/jquery.mobilemenu.js"></script>
    <script>
        // Funções para os gráficos
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Colunas (Tipo por Dados)
    const ctx = document.getElementById('graficoColunas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labelsTipo); ?>,
            datasets: [{
                label: 'Média do Sensor',
                data: <?php echo json_encode($mediasTipo); ?>,
                backgroundColor: '#4CAF50'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Linha (Dados por Data de Coleta)
    const ctx2 = document.getElementById('graficoLinha').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dataColeta); ?>,
            datasets: [{
                label: 'Leitura dos Sensores',
                data: <?php echo json_encode($dadosSensor); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Barras (Dados por Hora de Coleta)
    new Chart(document.getElementById('graficoHora'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($horaColeta); ?>,
            datasets: [{
                label: 'Dados por Hora',
                data: <?php echo json_encode($dadosSensor); ?>,
                backgroundColor: 'orange'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Barras (Média por Unidade de Medida)
    new Chart(document.getElementById('graficoMediaUnidade'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labelsUnidade); ?>,
            datasets: [{
                label: 'Média por Unidade',
                data: <?php echo json_encode($mediasUnidade); ?>,
                backgroundColor: 'green'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Dispersão (Latitude vs Dados)
    new Chart(document.getElementById('graficoLat'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Latitude vs Dados',
                data: <?php
                    $pontosLat = [];
                    for ($i = 0; $i < count($latitude); $i++) {
                        $pontosLat[] = [
                            "x" => floatval($latitude[$i]),
                            "y" => floatval($dadosSensor[$i])
                        ];
                    }
                    echo json_encode($pontosLat);
                ?>,
                backgroundColor: 'purple'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Dispersão (Longitude vs Dados)
    new Chart(document.getElementById('graficoLong'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Longitude vs Dados',
                data: <?php
                    $pontosLong = [];
                    for ($i = 0; $i < count($longitude); $i++) {
                        $pontosLong[] = [
                            "x" => floatval($longitude[$i]),
                            "y" => floatval($dadosSensor[$i])
                        ];
                    }
                    echo json_encode($pontosLong);
                ?>,
                backgroundColor: 'red'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Botão voltar ao topo
    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        const backToTopBtn = document.querySelector('.back-to-top');
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            backToTopBtn.style.display = 'flex';
        } else {
            backToTopBtn.style.display = 'none';
        }
    }

    document.querySelector('.back-to-top').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

    </script>
</body>
</html>