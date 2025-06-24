<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Cadastro ou Login</title>
    <meta name="theme-color" content="#005600">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link rel="stylesheet" href="../STYLES/cadastrar.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container">
      <div class="forms-container">
        <div class="signin-signup">
          <form action="../CONTROLLER/controller_usuario.php?tipo_acao=login" class="sign-in-form" method="POST">
            <h2 class="title">Entrar</h2>
            <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
              <p style="color: red; font-weight: bold; text-align: center;">Email ou senha incorretos!</p>
            <?php endif; ?>
            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email_E" placeholder="Email" required />
            </div>

            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="senha" placeholder="Senha" required />
            </div>

            <button type="submit" class="btn-3d">Entrar</button>

            <div class="forgot-password">
                <a href="esqueci_senha.php?id=<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" class="forgot-password">Esqueci minha senha?</a>
            </div>
          </form>

          <form action="../CONTROLLER/controller_usuario.php?tipo_acao=cadastro" class="sign-up-form" method="POST">
            <h2 class="title">Cadastrar</h2>

            <div class="form-row">
              <!-- Nome -->
              <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="nome" placeholder="Nome" required />
              </div>
              <!-- Sobrenome -->
              <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="sobrenome" placeholder="Sobrenome" required />
              </div>
            </div>

            <div class="form-row">
              <!-- Razão Social -->
              <div class="input-field">
                <i class="fas fa-building"></i>
                <input type="text" name="razao_social" placeholder="Razão Social (opcional)" />
              </div>
              <!-- CNPJ -->
              <div class="input-field">
                <i class="fas fa-id-card"></i>
                <input type="text" name="cnpj" placeholder="CNPJ (opcional)" />
              </div>
            </div>

            <div class="form-row">
              <!-- Email -->
              <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required />
              </div>
              <!-- Data de Nascimento -->
              <div class="input-field">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" name="DATA_NASCIMENTO" placeholder="Data de Nascimento" required />
              </div>
            </div>

            <div class="form-row">
              <!-- CPF -->
              <div class="input-field">
                <i class="fas fa-id-badge"></i>
                <input
                  type="text"
                  name="CPF"
                  placeholder="CPF (somente números, ex: 12345678901)"
                  pattern="\d{11}"
                  title="11 dígitos numéricos"
                  required
                />
              </div>
              <!-- Telefone -->
              <div class="input-field">
                <i class="fas fa-phone"></i>
                <input
                  type="text"
                  name="telefone"
                  placeholder="Telefone Celular (11 dígitos)"
                  pattern="\d{11}"
                  title="11 dígitos numéricos"
                  required
                />
              </div>
            </div>

            <div class="form-row">
              <div class="input-fields">
                  <i class="fas fa-lock"></i>
                  <input type="password" name="senha" placeholder="Senha" minlength="6" required />
              </div>
            </div>
        
            <input type="submit" class="btn-3d" value="Cadastrar" style="margin-top: 20px; width: 100%;" />
          </form>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>Novo Aqui?</h3>
            <p>Faça o Cadastro e libere o acesso ao site!</p>
            <button class="btn-transparent-3d" id="sign-up-btn">Cadastrar</button>
          </div>
          <img src="img/" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Possui Uma Conta?</h3>
            <p>Faça o Login e acesse sua conta!</p>
            <button class="btn-transparent-3d" id="sign-in-btn">Entrar</button>
          </div>
          <img src="" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="../SCRIPTS/cadastrar.js"></script>

    <script>
      document.querySelector('.sign-up-form').addEventListener('submit', function () {
        return true;
      });
    </script>
</body>
</html>