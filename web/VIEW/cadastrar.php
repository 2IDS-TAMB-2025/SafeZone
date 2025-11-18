<?php
if (isset($_GET['sucesso'])) {
    if ($_GET['sucesso'] == 1) {
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    }
    if ($_GET['sucesso'] == 2) {
        echo "<script>alert('Senha atualizada com sucesso!');</script>";
    }
}

?>
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
    <style>
      .esqueci_senha {
      text-align: center;
      margin-top: 10px;
      text-decoration: none;
    }

    .esqueci_senha a {
      color: #005600;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s;
    }

    .esqueci_senha a:hover {
      color: #008000;
      text-decoration: none;
    }
    </style>
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

            <button style="width: 64.5%;" type="submit" class="btn-3d">Entrar</button>

            <!-- Coloca o link DENTRO do form de login -->
            <p class="esqueci_senha">
              <a href="./esquecisenha.php">Esqueceu a Senha?</a>
            </p>
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

    <script>
function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    
    let soma = 0, resto;
    for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;
    
    soma = 0;
    for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf.substring(10, 11));
}

function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    if (cnpj.length === 0) return true; // opcional
    if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) return false;

    let tamanho = cnpj.length - 2
    let numeros = cnpj.substring(0, tamanho)
    let digitos = cnpj.substring(tamanho)
    let soma = 0
    let pos = tamanho - 7

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) pos = 9
    }
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
    if (resultado != digitos.charAt(0)) return false

    tamanho = tamanho + 1
    numeros = cnpj.substring(0, tamanho)
    soma = 0
    pos = tamanho - 7
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) pos = 9
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
    return resultado == digitos.charAt(1)
}

function validarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    return telefone.length === 11;
}

function validarFormulario() {
    const cpf = document.getElementById('cpf').value;
    const telefone = document.getElementById('telefone').value;
    const cnpj = document.getElementById('cnpj').value;

    if (!validarCPF(cpf)) {
        alert('CPF inválido!');
        return false;
    }
    if (!validarTelefone(telefone)) {
        alert('Telefone deve ter exatamente 11 dígitos.');
        return false;
    }
    if (!validarCNPJ(cnpj)) {
        alert('CNPJ inválido!');
        return false;
    }
    return true;
}
</script>
</body>
</html>