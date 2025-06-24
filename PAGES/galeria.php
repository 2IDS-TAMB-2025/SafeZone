<?php
  require_once('../MODEL/model_usuario.php');

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
    <title>Safe Zone | Galeria</title>
    <link rel="stylesheet" href="../STYLES/galeria.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        </header>
    </div>

    <div class="verde"></div>

    <div class="main">
        <main>
            <h1>Galeria de Imagens</h1>
            <div class="galeria">
                <div class="img-container">
                    <img src="../IMAGES/1.png" alt="Imagem 1">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/2.png" alt="Imagem 2">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/3.png" alt="Imagem 3">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/4.png" alt="Imagem 4">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/5.png" alt="Imagem 5">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/6.png" alt="Imagem 6">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/7.png" alt="Imagem 7">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/8.png" alt="Imagem 8">
                </div>
                <div class="img-container">
                    <img src="../IMAGES/9.png" alt="Imagem 9">
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

    <script src="../SCRIPTS/galeria.js"></script>
</body>
</html>