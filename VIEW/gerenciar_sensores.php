<?php
require_once '../MODEL/model_SENSORES.php';
require_once '../MODEL/model_usuario.php';

// Verificação de segurança no início do arquivo
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
// Redireciona se não estiver logado ou se não for administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['TIPO_USUARIO'] !== 'ADMINISTRADOR') {
    header('Location: ../VIEW/index.php');
    exit;
}


$id = $_SESSION['usuario']['ID_USUARIO'];

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
$sensorModel = new SensorModel();

// Processamento das ações (apenas para administradores)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'adicionar':
                $sensorModel->adicionarSensor(
                    $_POST['tipo'],
                    $_POST['localizacao'],
                    $_POST['data_instalacao'],
                    $_POST['status']
                );
                header("Location: gerenciar_sensores.php?id=$id");
                exit; // E esta
                break;
                
            case 'editar':
                $sensorModel->editarSensor(
                    $_POST['id'],
                    $_POST['tipo'],
                    $_POST['localizacao'],
                    $_POST['data_instalacao'],
                    $_POST['status']
                );
                header("Location: gerenciar_sensores.php?id=$id");
                exit; // E esta
                break;
        }
    }
}

// Exclusão (apenas para administradores)
if (isset($_GET['excluir'])) {
    $sensorModel->excluirSensor($_GET['excluir']);
    header("Location: ./gerenciar_sensores.php?id=$id");
    exit;
}

$sensores = $sensorModel->listarSensores();

// Separar sensores por status
$sensoresAtivos = array_filter($sensores, function($sensor) {
    return $sensor['STATUS_SENSOR'] === 'Ativo';
});

$sensoresInativos = array_filter($sensores, function($sensor) {
    return $sensor['STATUS_SENSOR'] === 'Inativo';
});

$sensoresManutencao = array_filter($sensores, function($sensor) {
    return $sensor['STATUS_SENSOR'] === 'Manutenção';
});

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Gerenciamento dos Sensores</title>
    <link rel="stylesheet" href="../STYLES/gerenciamento_sensores.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
                    <li><a href="<?php echo './mapas.php?id=' . $id; ?>">Mapas</a></li>
                    <li><a href="<?php echo './graficos.php?id=' . $id; ?>">Gráficos</a></li>
                    <li><a href="<?php echo './contato.php?id=' . $id; ?>">Contato</a></li>
                    <li><a href="<?php echo './sobre-nos.php?id=' . $id; ?>">Sobre Nós</a></li>
                </ul>
            </div>
            <div class="perfil">
                <a href="<?php echo './config.php?id=' . $id; ?>">
                    <img id="preview-img-profile" src="../IMAGES/PERFIL/<?php echo !empty($usu["FOTO_PERFIL"]) ? $usu["FOTO_PERFIL"] : 'PERFIL_SEM_FOTO.png'; ?>">
                </a>
            </div>
            <a href="?logout=1" class="logout-btn" title="Sair">
                <i class='bx bx-log-out'></i>
            </a>
        </header>
    </div>

    <div class="verde"></div>

    <div class="main">
        <main>
            <div class="container">
                <h1 class="titulo">Gerenciamento de Sensores</h1>
                
                <div class="centro">
                    <button class="btn btn-add" onclick="abrirModalAdicionar()">
                        <i class="fas fa-plus"></i> Adicionar Sensor
                    </button>
                </div>
                
                <!-- Sensores Ativos -->
                <div class="status-section">
                    <h2><i class="fas fa-circle" style="color: green;"></i> Sensores Ativos</h2>
                    <div class="sensor-grid">
                        <?php foreach ($sensoresAtivos as $sensor): ?>
                        <div class="sensor-card sensor-ativo" data-id="<?php echo $sensor['ID_SENSOR']; ?>">
                            <div class="sensor-header">
                                <div>
                                    <i class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> <?php echo htmlspecialchars($sensor['DATA_INSTALACAO']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($sensor['STATUS_SENSOR']); ?></p>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sensores em Manutenção -->
                <div class="status-section">
                    <h2><i class="fas fa-circle" style="color: orange;"></i> Sensores em Manutenção</h2>
                    <div class="sensor-grid">
                        <?php foreach ($sensoresManutencao as $sensor): ?>
                            <div class="sensor-card sensor-manutencao" data-id="<?php echo $sensor['ID_SENSOR']; ?>">
                                <div class="sensor-header">
                                <div>
                                    <i class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> <?php echo htmlspecialchars($sensor['DATA_INSTALACAO']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($sensor['STATUS_SENSOR']); ?></p>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sensores Inativos -->
                <div class="status-section">
                    <h2><i class="fas fa-circle" style="color: red;"></i> Sensores Inativos</h2>
                    <div class="sensor-grid">
                        <?php foreach ($sensoresInativos as $sensor): ?>
                            <div class="sensor-card sensor-inativo" data-id="<?php echo $sensor['ID_SENSOR']; ?>">
                            <div class="sensor-header">
                                <div>
                                    <i class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> <?php echo htmlspecialchars($sensor['DATA_INSTALACAO']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($sensor['STATUS_SENSOR']); ?></p>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal" id="modalSensor">
                <div class="modal-content">
                    <span class="close" onclick="fecharModal()">&times;</span>
                    <h2 id="tituloModal">Adicionar Sensor</h2>
                    <form method="POST">
                        <input type="hidden" name="acao" id="acao" value="adicionar">
                        <input type="hidden" name="id" id="id_sensor">

                        <div class="form-group">
                            <label>Tipo do Sensor</label>
                            <input type="text" name="tipo" id="tipo" required>
                        </div>

                        <div class="form-group">
                            <label>Localização</label>
                            <input type="text" name="localizacao" id="localizacao" required>
                        </div>

                        <div class="form-group">
                            <label>Data de Instalação</label>
                            <input type="date" name="data_instalacao" id="data_instalacao" required>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                                <option value="Manutenção">Manutenção</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button class="btn btn-add" type="submit">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

    <script src="../SCRIPTS/gerenciar_sensores.js"></script>
</body>
</html>