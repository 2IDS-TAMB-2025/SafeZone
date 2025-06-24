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
    <title>Safe Zone | Mapas</title>
    <link rel="stylesheet" href="../STYLES/mapas.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
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
        <div class="conteudo" data-anime="top">
            <h1>Mapa Zonas Seguras</h1>
            <p>Esse mapa mostra as áreas mais seguras perto das cidades com mais índices de desastre</p>
            <h3 id="gov">Mapa de Riscos:</h3>
            <p>Pesquise o nome da sua cidade, após isso, clique no sinal de alerta e adicione os tipos de risco que você quer saber sobre sua cidade</p>
        </div>
        
        <iframe src="https://geoportal.sgb.gov.br/desastres/" data-anime="bottom"></iframe>
        <div class="conteudo">
            <p style="color: #b8b6b6;">acesso em: https://geoportal.sgb.gov.br/desastres/</p>
        </div>
        <div id="map" data-anime="bottom"></div>
        <div class="conteudo">
            <p style="color: #b8b6b6;">Ultima atualização: 24/06/2025</p>
        </div>
        <section class="info-section">
            <h2 data-anime="left">Cidades com Riscos Ambientais e Zonas Seguras</h2>
            <div class="cards-container">
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Petrópolis (RJ)</h3>
                    <p><strong>Risco:</strong> Chuvas intensas e deslizamentos.</p>
                    <p><strong>Zona segura:</strong> Valparaíso e centro <br>plano.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Belo Horizonte (MG)</h3>
                    <p><strong>Risco:</strong> Alagamentos e encostas instáveis.</p>
                    <p><strong>Zona segura:</strong> Lourdes, Savassi, Funcionários.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Recife (PE)</h3>
                    <p><strong>Risco:</strong> Enchentes e desabamentos.</p>
                    <p><strong>Zona segura:</strong> Boa Viagem, Graças, Ilha do Leite.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Salvador (BA)</h3>
                    <p><strong>Risco:</strong> Deslizamentos em morros.</p>
                    <p><strong>Zona segura:</strong> Pituba, Itaigara, Caminho das Árvores.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>São Paulo (SP)</h3>
                    <p><strong>Risco:</strong> Alagamentos na ZL e ZS.</p>
                    <p><strong>Zona segura:</strong> Pinheiros, Butantã, Centro expandido.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Rio de Janeiro (RJ)</h3>
                    <p><strong>Risco:</strong> Deslizamentos em favelas.</p>
                    <p><strong>Zona segura:</strong> Barra da Tijuca, Copacabana, Ipanema.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Blumenau (SC)</h3>
                    <p><strong>Risco:</strong> Enchentes no vale do Itajaí.</p>
                    <p><strong>Zona segura:</strong> Garcia (parte alta), Escola Agrícola.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
                
                <div class="info-card" data-anime="right">
                    <div class="card-icon">
                        <i class='bx bxs-map'></i>
                    </div>
                    <h3>Manaus (AM)</h3>
                    <p><strong>Risco:</strong> Alagamentos em áreas ribeirinhas.</p>
                    <p><strong>Zona segura:</strong> Adrianópolis, Parque Dez, Aleixo.</p>
                    <div class="card-footer">
                        <span class="safe-tag">Área Segura</span>
                    </div>
                </div>
            </div>
        </section>

        <h2 data-anime="left">Zonas mais seguras próximas a cidades de alto risco</h2>

        <section class="cards-container">
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Petrópolis (RJ)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Valparaíso e áreas centrais mais planas</p>
                    <p><strong>Por quê?</strong> Melhor drenagem, relevo menos acidentado, infraestrutura consolidada.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Belo Horizonte (MG)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Lourdes, Savassi, Funcionários</p>
                    <p><strong>Por quê?</strong> Terreno estável, longe de encostas.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Recife (PE)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Boa Viagem, Ilha do Leite, Graças</p>
                    <p><strong>Por quê?</strong> Áreas planas com boa infraestrutura.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Salvador (BA)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Pituba, Caminho das Árvores, Itaigara</p>
                    <p><strong>Por quê?</strong> Fora de encostas, bem urbanizadas.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>São Paulo (SP)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Zona Oeste (Pinheiros, Butantã), Centro expandido</p>
                    <p><strong>Por quê?</strong> Menos enchentes comparado à Zona Leste/Sul.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Rio de Janeiro (RJ)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Barra da Tijuca, Copacabana, Ipanema</p>
                    <p><strong>Por quê?</strong> Planas, com infraestrutura de drenagem e contenção.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>

            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Blumenau (SC)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Garcia (parte alta), Escola Agrícola</p>
                    <p><strong>Por quê?</strong> Menos sujeitos à cheia do Rio Itajaí-Açu.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
            
            <div class="card" data-anime="bottom">
                <div class="card-header">
                    <i class='bx bxs-shield-alt'></i>
                    <h3>Manaus (AM)</h3>
                </div>
                <div class="card-body">
                    <p><strong>Zona segura:</strong> Adrianópolis, Parque Dez, Aleixo</p>
                    <p><strong>Por quê?</strong> Fora das áreas ribeirinhas, boa elevação.</p>
                </div>
                <div class="card-footer">
                    <span class="tag safe">Seguro</span>
                    <span class="tag low-risk">Baixo Risco</span>
                </div>
            </div>
        </section>
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
                    <li><a href="#">Políticas de Privacidade</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">Perguntas Frequentes</a></li>
                    <li><a href="#">Quem Somos</a></li>
                    <li><a href="#">Contato</a></li>
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
    
    <script src="../SCRIPTS/mapas.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</body>
</html>