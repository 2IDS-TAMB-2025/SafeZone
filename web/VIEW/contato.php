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
            <a href="./index.php<?php echo !empty($id) ? '?id='.$id : ''; ?>"><img class="img" src="../IMAGES/LOGO2.png" alt=""></a>
            <div class="campos" id="menuLinks">>
                <ul>
                    <li><a href="./index.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Início</a></li>
                    <li><a href="./galeria.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Galeria</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="./mapas.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Mapa</a></li>
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
    <main class="contact-container">
        <section class="contact-header">
            <h1>Entre em Contato</h1>
            <p>Tem dúvidas, sugestões ou precisa de ajuda? Nossa equipe está pronta para atendê-lo.</p>
        </section>

        <div class="contact-content">
            <section class="contact-form">
                <h2>Envie sua Mensagem</h2>
        <form action="mailto:safezone269@gmail.com" method="POST" enctype="text/plain">
            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input type="text" id="name" name="Nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="Email" required>
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
                <textarea id="message" name="Mensagem" rows="6" required></textarea>
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
                        <p>safezone269@gmail.com</p>
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
                        <a href="https://www.instagram.com/safe_zone.2025/" aria-label="Instagram" target="_blank"><i class='bx bxl-instagram'></i></a>
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
    
    <script src="../SCRIPTS/contato.js"></script>
    <script>
    // Controle do header no scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        const scrollPosition = window.scrollY;
        
        if (scrollPosition > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Botão voltar ao topo
    const backToTopButton = document.querySelector('.back-to-top');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
 <script>
    const modal = document.getElementById("modalPolitica");
    const btn = document.getElementById("abrirPolitica");
    const fechar = document.querySelector(".fechar");

    // abre o modal
    btn.onclick = function(e) {
        e.preventDefault(); // evita reload da página
        modal.style.display = "block";
    }

    // fecha ao clicar no X
    fechar.onclick = function() {
        modal.style.display = "none";
    }

    // fecha ao clicar fora da janela
    window.onclick = function(e) {
        if (e.target === modal) {
        modal.style.display = "none";
        }
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