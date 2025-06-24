<?php
require_once('../CONTROLLER/controller_perfil.php');

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
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Configurações</title>
    <link rel="stylesheet" href="../STYLES/config.css">
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
        .logout-btn{
            color:#007701;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 17px;
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
            <a href="<?php echo './index.php?id=' . $id; ?>"><img class="img" src="../IMAGES/LOGO.png" alt="Logo Safe Zone"></a>
            <div class="campos">
                <ul>
                    <li><a href="<?php echo './index.php?id=' . $id; ?>">Início</a></li>
                </ul>
            </div>
        </header>
    </div>

    <div class="main">
        <nav>
            <ul>
                <li><a href="#" class="active">Geral</a></li>
            </ul>
        </nav>
        
        <div class="conteudo">
            <form action="../CONTROLLER/controller_perfil.php?tipo_acao=editar" method="POST" enctype="multipart/form-data">
                <?php
                    foreach ($usuario as $usu){
                ?>
                <div class="media align-items-center">
                    <img id="preview-img-profile" src="<?php echo (!empty($usu['FOTO_PERFIL'])) ? '../IMAGES/PERFIL/' . $usu['FOTO_PERFIL'] : '../IMAGES/PERFIL/PERFIL_SEM_FOTO.png'; ?>"  alt="" />
                    <div class="media-body">
                        <label class="btn-primario">
                            Enviar nova foto
                            <input type="file" name="foto_perfil" id="input-img" class="account-settings-fileinput" disabled />
                        </label>
                        &nbsp;
                        <button type="button" class="btn-primario" id="reset-img">Redefinir</button>
                        <div class="text">Permitido JPG, GIF ou PNG. Tamanho máximo de 800KB</div>
                    </div>
                </div>

                <input style="display: none; visibility: hidden;" type="text" name="id" id="id" value="<?php echo  $usu["ID_USUARIO"]; ?>">
                
                <div class="form-group">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" id="usuario" value="<?php echo  $usu["NOME"]; ?>" class="form-control" placeholder="Digite seu Nome..." readonly />
                </div>

                <div class="form-group">
                    <label class="form-label">Sobrenome</label>
                    <input type="text" name="sobrenome" id="nome" value="<?php echo  $usu["SOBRENOME"]; ?>" class="form-control" placeholder="Digite seu Sobrenome..."  readonly />
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="nome-completo" value="<?php echo  $usu["EMAIL"]; ?>" class="form-control"  readonly/>
                </div>

                <div class="form-group">
                    <label class="form-label">Data de nascimento</label>
                    <input type="date" name="data_nascimento" id="data-nascimento" value="<?php echo  $usu["DATA_NASCIMENTO"]; ?>" class="form-control" readonly/>
                </div>

                <div class="form-group">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" value="<?php echo  $usu["CPF"]; ?>" class="form-control" readonly />
                </div>

                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" value="<?php echo  $usu["SENHA"]; ?>" class="form-control" readonly  />
                </div>

                <div class="form-group">
                    <label class="form-label">Número de telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?php echo  $usu["TELEFONE_CELULAR"]; ?>" class="form-control" readonly/>
                </div>

                <div class="form-group">
                    <label class="form-label">Razão Social</label>
                    <input type="text" name="razao_social" value="<?php echo  $usu["RAZAO_SOCIAL"]; ?>" class="form-control" readonly />
                </div>

                <div class="form-group">
                    <label class="form-label">CNPJ</label>
                    <input type="text" name="cnpj" value="<?php echo  $usu["CNPJ"]; ?>" class="form-control" readonly />
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de Usuário</label>
                    <select name="tipo_usuario" class="form-control">
                        <option value="USUARIO" <?php echo ($usu["TIPO_USUARIO"] === 'USUARIO') ? 'selected' : ''; ?>>Usuário</option>
                        <option value="ADMINISTRADOR" <?php echo ($usu["TIPO_USUARIO"] === 'ADMINISTRADOR') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="alert-warning">
                        Seu e-mail não está confirmado. Verifique sua caixa de entrada.<br />
                        <a href="javascript:void(0)" style="color: green;">Reenviar confirmação</a>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="btn-primario" id="btn-editar">Editar</button>
                    <a href="?logout=1" class="logout-btn" title="Sair">
                        Sair
                    </a>
                    <button type="submit" class="btn-primario" id="btn-salvar" style="display: none;">Salvar</button>
                    &nbsp;
                    <a href="../CONTROLLER/controller_perfil.php?tipo_acao=excluir&id=<?php echo $usu["ID_USUARIO"]; ?>" class="cancelar" onclick="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.');">Excluir</a>
                </div>
                <?php
                    }
                ?>
            </form>
        </div>
    </div>

    <button class="back-to-top" title="Voltar ao topo">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../SCRIPTS/config.js"></script>
</body>
</html>