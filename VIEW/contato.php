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
    <title>Safe Zone | Contate-nos</title>
    <link rel="stylesheet" href="../STYLES/contato.css">
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
            <a href="<?php echo './index.php?id=' . $id; ?>"><img class="img" src="../IMAGES/LOGO.png" alt=""></a>
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

<div class="main">
    <main class="contact-container">
        <section class="contact-header">
            <h1>Entre em Contato</h1>
            <p>Tem dúvidas, sugestões ou precisa de ajuda? Nossa equipe está pronta para atendê-lo.</p>
        </section>

        <div class="contact-content">
            <section class="contact-form">
                <h2>Envie sua Mensagem</h2>
                <form action="../CONTROLLER/controller_contato.php?tipo_acao=editar" method="POST" id="contactForm">
                    <div class="form-group">
                        <label for="name">Nome Completo</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Assunto</label>
                        <select id="subject" name="subject" required>
                            <option value="" disabled selected>Selecione um assunto</option>
                            <option value="duvida">Dúvida</option>
                            <option value="sugestao">Sugestão</option>
                            <option value="problema">Reportar Problema</option>
                            <option value="parceria">Parceria</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mensagem</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Enviar Mensagem</button>
                </form>
            </section>

            <section class="contact-info">
                <h2>Informações de Contato</h2>
                
                <div class="info-card">
                    <i class='bx bxs-phone'></i>
                    <div>
                        <h3>Telefone</h3>
                        <p>+55 (19) 9935-7890</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class='bx bxs-envelope'></i>
                    <div>
                        <h3>E-mail</h3>
                        <p>safezone@gmail.com</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class='bx bxs-time'></i>
                    <div>
                        <h3>Horário de Atendimento</h3>
                        <p>Segunda a Sexta: 08h às 18h</p>
                        <p>Sábado: 08h às 12h</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <i class='bx bxs-map'></i>
                    <div>
                        <h3>Endereço</h3>
                        <p>Rua Safe Zone, 123 - Centro</p>
                        <p>Tambaú - SP, 13710-000</p>
                    </div>
                </div>
                
                <div class="social-media">
                    <h3>Redes Sociais</h3>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class='bx bxl-facebook'></i></a>
                        <a href="#" aria-label="Instagram"><i class='bx bxl-instagram'></i></a>
                        <a href="#" aria-label="Twitter"><i class='bx bxl-twitter'></i></a>
                        <a href="#" aria-label="LinkedIn"><i class='bx bxl-github' ></i></a>
                        <a href="#" aria-label="WhatsApp"><i class='bx bxl-whatsapp'></i></a>
                    </div>
                </div>
            </section>
        </div>

        <section class="contact-map">
            <h2>Onde Estamos</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3691.185811950736!2d-47.2749187!3d-22.3139243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c7db4d963b6e1d%3A0x8f1a6e1e0e1e1e1e!2sTamba%C3%BA%2C%20SP%2C%2013710-000!5e0!3m2!1spt-BR!2sbr!4v1620000000000!5m2!1spt-BR!2sbr" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </section>
    </main>
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
    
    <script src="../SCRIPTS/contato.js"></script>
</body>
</html>