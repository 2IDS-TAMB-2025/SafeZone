<?php
  require_once('../MODEL/model_usuario.php');

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
    <title>Safe Zone | Monitoramento Inteligente</title>
    <link rel="stylesheet" href="../STYLES/index.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <a href="./index.php<?php echo !empty($id) ? '?id='.$id : ''; ?>"><img class="img" src="../IMAGES/LOGO2.png" alt=""></a>
            <div class="campos">
                <ul>
                    <li><a href="./index.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Início</a></li>
                    <li><a href="./galeria.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Galeria</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="./mapas.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Mapas</a></li>
                    <li><a href="./graficos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Gráficos</a></li>
                    <li><a href="./contato.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Contato</a></li>
                    <?php endif; ?>
                    <li><a href="./sobre-nos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Sobre Nós</a></li>
                </ul>
            </div>
            <?php if(!empty($id)): ?>
            <div class="perfil">
                <a href="<?php echo './config.php?id=' . $id; ?>">
                    <img style="border-radius: 50%;" src="../IMAGES/PERFIL/<?php echo !empty($usu["FOTO_PERFIL"]) ? $usu["FOTO_PERFIL"] : 'PERFIL_SEM_FOTO.png'; ?>">
                </a>
            </div>
            <?php endif; ?>
            <?php if(!empty($id)): ?>
            <a href="?logout=1" class="logout-btn" title="Sair">
                <i class='bx bx-log-out'></i>
            </a>
            <?php endif; ?>
        </header>
    </div>

    <div class="conteudo">
        <div class="text">
            <h1>MONITORAMENTO <span>INTELIGENTE</span></h1>
            <p>Nosso compromisso é fornecer soluções eficientes para o monitoramento e prevenção de riscos ambientais, garantindo um ambiente mais seguro para todos.</p>
        </div>
        <div class="btn-entrar-cadastrar">
            <?php
                if(empty($id)){
            ?>
            <a href="./cadastrar.php"><button>LOGIN</button></a>
            <a href="./cadastrar.php"><button>CADASTRO</button></a>
            <?php
                }
            ?>
        </div>
    </div>

    <div style="margin-top: 20%;" class="conteudo2">
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

    <div class="conteudo3">
        <h1>Monitorar, Prevenir E Preservar: Nosso Compromisso com o Meio Ambiente.</h1>
        <div class="bloc">
            <div class="bloc1">
                <div class="bloco2">
                    <i class="fa-solid fa-industry"></i>
                    <div class="texto2">
                        <h3>Poluição do Ar</h3>
                        <p>Conscientizar sobre seus impactos e incentivar ações por um ar mais limpo.</p>
                    </div>
                </div>
                <div class="bloco2">
                    <i class="fa-solid fa-tree"></i>
                    <div class="texto2">
                        <h3>Desmatamento</h3>
                        <p>Alertar sobre os danos às florestas e promover sua preservação.</p>
                    </div>
                </div>
                <div class="bloco2">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                    <div class="texto2">
                        <h3>Incêndios Florestais</h3>
                        <p>Informar sobre causas e prevenir queimadas com respeito à natureza.</p>
                    </div>
                </div>
            </div>
            <div class="bloc2">
                <div class="bloco2">
                    <i class="fa-solid fa-dove"></i>
                    <div class="texto2">
                        <h3>Fauna em Risco</h3>
                        <p>Proteger a vida animal e estimular a preservação das espécies.</p>
                    </div>
                </div>
                <div class="bloco2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div class="texto2">
                        <h3>Áreas de Risco</h3>
                        <p>Mostrar os perigos da ocupação desordenada e defender soluções.</p>
                    </div>
                </div>
                <div class="bloco2">
                    <i class="fa-solid fa-leaf"></i>
                    <div class="texto2">
                        <h3>Soluções Sustentáveis</h3>
                        <p>Promover atitudes que contribuam para um futuro mais equilibrado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="conteudo4">
        <h1>Notícia Importante</h1>
        <div class="noticia">
            <h2>Amazônia Registra Recorde De Queimadas, E Fumaça Atinge 10 Estados.</h2>
            <p>Desde janeiro, já foram registrados mais de 63 mil focos de incêndio — o maior número desde 2014. A fumaça das queimadas, somada à intensa seca que atinge mais de mil cidades brasileiras, cobre o céu com uma camada cinza e torna o ar difícil de respirar. Esse volume de fumaça se espalha por milhares de quilômetros, vindo também do Pantanal, de Rondônia (onde houve uma grande queimada no Parque Guajará-Mirim) e da Bolívia, formando um "corredor de fumaça" que avança por várias regiões do país.</p>
            <a href="https://g1.globo.com/meio-ambiente/noticia/2024/08/21/amazonia-tem-pior-temporada-de-queimadas-em-17-anos-corredor-de-fumaca-se-espalha-e-afeta-10-estados.ghtml">Leia a notícia completa</a>
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

    <script src="../SCRIPTS/index.js"></script>
</body>
</html>