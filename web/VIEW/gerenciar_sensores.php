<?php
require_once '../MODEL/model_SENSORES.php';
require_once '../MODEL/model_usuario.php';

session_start();

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: ../VIEW/index.php");
    exit;
}

if (!isset($_SESSION['usuario'])) {
    if (isset($_GET['id'])) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioId($_GET['id']);

        if (!empty($usuario) && $usuario[0]['TIPO_USUARIO'] === 'ADMINISTRADOR') {
            $_SESSION['usuario'] = $usuario[0]; // cria sessão automaticamente
        } else {
            header("Location: ../VIEW/index.php");
            exit;
        }
    } else {
        header("Location: ../VIEW/index.php");
        exit;
    }
}


$id = $_SESSION['usuario']['ID_USUARIO'];
$usu = $_SESSION['usuario'];

$sensorModel = new SensorModel();


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
                exit;
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
                exit;
                break;
        }
    }
}


if (isset($_GET['excluir'])) {
    $sensorModel->excluirSensor($_GET['excluir']);
    header("Location: ./gerenciar_sensores.php?id=$id");
    exit;
}

$sensores = $sensorModel->listarSensores();


if (!isset($_GET['cidade']) || $_GET['cidade'] == "") {
    $sensores = [];
    $sensoresAtivos = [];
    $sensoresInativos = [];
    $sensoresManutencao = [];
} else {
    $cidade = $_GET['cidade'];
    $sensores = $sensorModel->filtrarPorLocalizacao($cidade);

    $sensoresAtivos = array_filter($sensores, function($sensor) {
        return $sensor['STATUS_SENSOR'] === 'Ativo';
    });

    $sensoresInativos = array_filter($sensores, function($sensor) {
        return $sensor['STATUS_SENSOR'] === 'Inativo';
    });

    $sensoresManutencao = array_filter($sensores, function($sensor) {
        return $sensor['STATUS_SENSOR'] === 'Manutenção';
    });
}


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
        /* Container geral do filtro */
.filtro-cidade-form {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px auto 30px;
    padding: 12px 18px;
    background: #f1f5f4;
    border-radius: 10px;
    width: fit-content;
    box-shadow: 0 2px 6px rgba(0,0,0,0.07);
}

/* Grupo do select */
.filtro-group select {
    padding: 10px 14px;
    font-size: 15px;
    border: 1px solid #cdd5ce;
    border-radius: 8px;
    background: #ffffff;
    color: #333;
    cursor: pointer;
    transition: 0.2s ease;
}

.filtro-group select:hover {
    border-color: #7bb582;
}

/* Botões */
.filtro-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s ease;
    text-decoration: none;
}

/* Botão aplicar */
.filtro-btn-aplicar {
    background: #2f9e44;
    color: #fff;
}

.filtro-btn-aplicar:hover {
    background: #38b04e;
    transform: scale(1.05);
}

/* Botão limpar */
.filtro-btn-limpar {
    background: #d00000;
    color: #fff;
}

.filtro-btn-limpar:hover {
    background: #e03131;
    transform: scale(1.05);
}

    </style>
</head>
<body>
    <div class="header">
        <header>
            <a href="./contato.php<?php echo !empty($id) ? '?id='.$id : ''; ?>"><img class="img" src="../IMAGES/LOGO2.png" alt=""></a>
            <div class="campos" id="menuLinks">
                <ul>
                    <li><a href="./index.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Início</a></li>
                    <li><a href="./galeria.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Galeria</a></li>
                    <li><a href="./mapas.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Mapa</a></li>
                    <li><a href="./graficos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Gráficos</a></li>
                    <li><a href="./contato.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Contato</a></li>
                    <li><a href="./sobre-nos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Sobre Nós</a></li>
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
            <div class="menu-mobile" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>
        </header>
    </div>

    <div class="verde"></div>

    <div class="main">
        <main>
            <div class="container">
                <h1 class="titulo">Gerenciamento de Sensores</h1>
                <form method="GET" class="filtro-cidade-form">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <select name="cidade" class="filtro-select">
        <option value="">Filtrar por Localização</option>
        <option value="Tambaú - Escola Sesi" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Tambaú - Escola Sesi") echo 'selected'; ?>>Tambaú - Escola Sesi</option>
        <option value="Tambaú - Prefeitura" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Tambaú - Prefeitura") echo 'selected'; ?>>Tambaú - Prefeitura</option>
        <option value="Tambaú - São Lourenço" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Tambaú - São Lourenço") echo 'selected'; ?>>Tambaú - São Lourenço</option>
        <option value="Tambaú - Serra" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Tambaú - Serra") echo 'selected'; ?>>Tambaú - Serra</option>
        <option value="Palmeiras - Prefeitura" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Palmeiras - Prefeitura") echo 'selected'; ?>>Palmeiras - Prefeitura</option>
        <option value="Palmeiras - Jardim Santa Clara" <?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Palmeiras - Jardim Santa Clara") echo 'selected'; ?>>Palmeiras - Jardim Santa Clara</option>
        <option value="Palmeiras - Vila dos Oficias"<?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Palmeiras - Vila dos Oficias") echo 'selected'; ?>>Palmeiras - Vila dos Oficias</option>
        <option value="Palmeiras - Santo Antônio"<?php if(isset($_GET['cidade']) && $_GET['cidade'] == "Palmeiras - Santo Antônio") echo 'selected'; ?>>Palmeiras - Santo Antônio</option>
</select>



    <button type="submit" class="filtro-btn filtro-btn-aplicar">
        <i class='bx bx-filter'></i> Filtrar
    </button>

    <?php if(isset($_GET['cidade']) && $_GET['cidade'] != ""): ?>
        <a href="gerenciar_sensores.php?id=<?php echo $id; ?>" class="filtro-btn filtro-btn-limpar">
            <i class='bx bx-x-circle'></i> Limpar filtro
        </a>
    <?php endif; ?>
</form>

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
                                    <i style="color: green;" class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button style="background-color: green;" class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button style="background-color: green;" class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> 
                                <?php 
                                    $data = new DateTime($sensor['DATA_INSTALACAO']);
                                    echo htmlspecialchars($data->format('d/m/Y')); 
                                ?>
                            </p>
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
                                    <i style="color: orange;" class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button style="background-color: orange;" class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button style="background-color: orange;" class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> 
                                <?php 
                                    $data = new DateTime($sensor['DATA_INSTALACAO']);
                                    echo htmlspecialchars($data->format('d/m/Y')); 
                                ?>
                            </p>
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
                                    <i style="color: red;" class="fas fa-microchip sensor-icon"></i>
                                    <strong><?php echo htmlspecialchars($sensor['TIPO_SENSOR']); ?></strong>
                                </div>
                                <div class="sensor-actions">
                                    <button style="background-color: red;" class="btn btn-edit" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($sensor)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button style="background-color: red;" class="btn btn-delete" onclick="confirmarExclusao(<?php echo $sensor['ID_SENSOR']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($sensor['LOCALIZACAO']); ?></p>
                            <p><strong>Data Instalação:</strong> 
                                <?php 
                                    $data = new DateTime($sensor['DATA_INSTALACAO']);
                                    echo htmlspecialchars($data->format('d/m/Y')); 
                                ?>
                            </p>
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
                                <select name="tipo" id="tipo" required>
                                    <option value="" disabled selected>Selecione o tipo de sensor</option>
                                    <option value="Sensor de Temperatura">Sensor de Temperatura</option>
                                    <option value="Sensor de Umidade">Sensor de Umidade</option>
                                    <option value="Sensor de Gases">Sensor de Gases</option>
                                    <option value="Sensor Ultrassônico">Sensor Ultrassônico</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Cidade / Região</label>
                             <select name="cidade_base" id="cidade_base" required onchange="filtrarLocalizacao()">
                                    <option value="" disabled selected>Selecione a cidade</option>
                                    <option value="Tambaú">Tambaú</option>
                                     <option value="Palmeiras">Palmeiras</option>
                             </select>
                            </div>

                            <div class="form-group" id="localizacao_group" style="display: none;"> 
                                <label>Local Específico</label>
                                <select name="localizacao" id="localizacao" required disabled>
                                    <option value="" disabled selected>Selecione um local</option>

                                    <!-- Tambaú -->
                                    <optgroup label="Tambaú" class="opcao-tambaú" style="display:none;">
                                        <option value="Tambaú - Escola Sesi">Escola Sesi</option>
                                        <option value="Tambaú - Prefeitura">Prefeitura</option>
                                        <option value="Tambaú - Serra">Serra</option>
                                        <option value="Tambaú - São Lourenço">São Lourenço</option>
                                    </optgroup>

                                    <!-- Palmeiras -->
                                    <optgroup label="Palmeiras" class="opcao-palmeiras" style="display:none;">
                                        <option value="Palmeiras - Prefeitura">Prefeitura</option>
                                        <option value="Palmeiras - Vila dos Oficias">Vila dos Oficias</option>
                                        <option value="Palmeiras - Santo Antônio">Santo Antônio</option>
                                        <option value="Palmeiras - Jardim Santa Clara">Jardim Santa Clara</option>
                                    </optgroup>

                                </select>
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
                <p style="text-align: justify;">O Safe Zone é um espaço seguro dedicado a fornecer suporte, informações e recursos para aqueles que precisam de ajuda. Nossa missão é criar uma comunidade acolhedora e acessível para todos.</p>
            </div>
            <div class="links">
                <h4>Links Úteis</h4>
                <ul>
                    <li><a href="./sobre-nos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Quem Somos</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="./contato.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Contato</a></li>
                    <?php endif; ?>
                    <li><a href="#" id="abrirPolitica">Políticas de Privacidade</a></li>
                </ul>

                <!-- Modal escondido -->
                <div id="modalPolitica" class="modal">
                <div class="modal-content">
                    <span class="fechar">&times;</span>
                    <h2>Políticas de Privacidade – SafeZone</h2>

                    <p>A SafeZone respeita a sua privacidade e está comprometida em proteger os dados pessoais dos usuários e as informações coletadas pelos nossos sistemas de monitoramento.</p>

                    <h3>1. Coleta de Dados</h3>
                    <ul>
                    <li>Utilizamos sensores de <strong>gases, temperatura e umidade</strong> para monitorar o ambiente em tempo real, especialmente em áreas de risco de queimadas e desastres ambientais.</li>
                    <li>Em nosso site e aplicativo, poderemos coletar informações fornecidas voluntariamente pelos usuários, como nome, e-mail e dados de contato para cadastro, acesso à plataforma e envio de notificações.</li>
                    </ul>

                    <h3>2. Uso das Informações</h3>
                    <p>As informações coletadas são utilizadas para:</p>
                    <ul>
                    <li>Monitorar e analisar riscos ambientais em tempo real.</li>
                    <li>Emitir alertas de segurança e recomendações preventivas.</li>
                    <li>Melhorar os serviços oferecidos pela SafeZone.</li>
                    <li>Entrar em contato com usuários, quando necessário, em situações de risco ou suporte técnico.</li>
                    </ul>

                    <h3>3. Compartilhamento de Dados</h3>
                    <ul>
                    <li>Não compartilhamos dados pessoais dos usuários com terceiros sem consentimento, exceto em casos legais ou para proteção da segurança pública.</li>
                    <li>Informações ambientais coletadas pelos sensores poderão ser utilizadas em relatórios, estudos e parcerias com órgãos de monitoramento e defesa civil.</li>
                    </ul>

                    <h3>4. Segurança das Informações</h3>
                    <ul>
                    <li>Adotamos medidas técnicas e organizacionais adequadas para proteger seus dados contra acessos não autorizados, uso indevido ou divulgação indevida.</li>
                    <li>O acesso às informações é restrito apenas a pessoas autorizadas e treinadas.</li>
                    </ul>

                    <h3>5. Direitos dos Usuários</h3>
                    <ul>
                    <li>Solicitar acesso, correção ou exclusão de seus dados pessoais.</li>
                    <li>Revogar o consentimento para uso das informações, quando aplicável.</li>
                    <li>Obter informações claras sobre o tratamento dos dados coletados.</li>
                    </ul>

                    <h3>6. Alterações nesta Política</h3>
                    <p>A SafeZone poderá atualizar esta Política de Privacidade periodicamente para refletir melhorias ou mudanças em nossos serviços. Recomendamos que os usuários revisem este documento regularmente.</p>

                    <h3>7. Contato</h3>
                    <p>Em caso de dúvidas sobre esta Política de Privacidade ou sobre o uso de seus dados, entre em contato com nossa equipe através do canal oficial de suporte.</p>

                </div>
                </div>
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
         <script>
    const modal = document.getElementById("modalPolitica");
    const btn = document.getElementById("abrirPolitica");
    const fechar = document.querySelector(".fechar");

    // abre o modal
    btn.onclick = function(e) {
        e.preventDefault(); 
        modal.style.display = "block";
    }

    fechar.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(e) {
        if (e.target === modal) {
        modal.style.display = "none";
        }
    }

function filtrarLocalizacao() {
    const cidadeSelect = document.getElementById('cidade_base');
    const localizacaoGroup = document.getElementById('localizacao_group');
    const localizacaoSelect = document.getElementById('localizacao');
    const cidadeSelecionada = cidadeSelect.value;
    
    const optgroups = localizacaoSelect.querySelectorAll('optgroup');
    optgroups.forEach(group => {
        group.style.display = 'none';
    });
    
    localizacaoSelect.value = "";
    localizacaoSelect.setAttribute('disabled', 'disabled');
    localizacaoGroup.style.display = 'none';

    if (cidadeSelecionada) {
        const grupoParaMostrar = localizacaoSelect.querySelector(`.opcao-${cidadeSelecionada.toLowerCase()}`);
        
        if (grupoParaMostrar) {
            grupoParaMostrar.style.display = 'block';
            localizacaoSelect.removeAttribute('disabled');
            localizacaoGroup.style.display = 'block';
        }
    }
}


function abrirModalAdicionar() {
    document.getElementById('tituloModal').innerText = 'Adicionar Sensor';
    document.getElementById('acao').value = 'adicionar';
    document.getElementById('id_sensor').value = '';
    
    
    document.getElementById('tipo').value = ""; 
    document.getElementById('cidade_base').value = ""; 
    filtrarLocalizacao(); 
    document.getElementById('data_instalacao').value = '';
    document.getElementById('status').value = 'Ativo'; 
    
    document.getElementById('modalSensor').style.display = 'block';
}


function abrirModalEditar(sensor) {
    document.getElementById('tituloModal').innerText = 'Editar Sensor';
    document.getElementById('acao').value = 'editar';

    document.getElementById('id_sensor').value = sensor.ID_SENSOR;
    document.getElementById('tipo').value = sensor.TIPO_SENSOR; 
    document.getElementById('data_instalacao').value = sensor.DATA_INSTALACAO;
    document.getElementById('status').value = sensor.STATUS_SENSOR;

    const localizacaoCompleta = sensor.LOCALIZACAO; 
    let cidadeBase = '';
    
    if (localizacaoCompleta.includes('Tambaú')) {
        cidadeBase = 'Tambaú';
    } else if (localizacaoCompleta.includes('Palmeiras')) {
        cidadeBase = 'Palmeiras';
    } 
    document.getElementById('cidade_base').value = cidadeBase;
    filtrarLocalizacao(); 
    if (cidadeBase) {
         document.getElementById('localizacao').value = localizacaoCompleta;
    }

    document.getElementById('modalSensor').style.display = 'block';
}

    </script>
    <script>
function toggleMenu() {
    const menu = document.getElementById("menuLinks");
    menu.classList.toggle("ativo");
}
</script>
    </body>
    </html>