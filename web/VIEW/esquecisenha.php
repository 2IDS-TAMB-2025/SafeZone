<?php
// Iniciar sessão para mensagens de erro/sucesso
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <title>Safe Zone | Esqueci minha senha</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #006400;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #000;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f4f7ff;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #006400;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #004d00;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #006400;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Esqueci minha senha</h2>
        
        <?php if (isset($_GET['erro'])): ?>
            <div class="erro" style="color: red; margin-bottom: 15px;">
                <?php 
                    switch($_GET['erro']) {
                        case 'limite': echo 'Limite de solicitações atingido. Tente novamente amanhã.'; break;
                        case 'email': echo 'Erro ao enviar e-mail. Tente novamente.'; break;
                        case 'banco': echo 'Erro no sistema. Tente novamente.'; break;
                        default: echo 'Ocorreu um erro. Tente novamente.';
                    }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="sucesso" style="color: green; margin-bottom: 15px;">
                Se o e-mail existir em nosso sistema, você receberá um código de recuperação.
            </div>
        <?php endif; ?>
        
        <form action="../CONTROLLER/controller_recuperacao_senha.php" method="POST" id="contato-form">
            <input type="email" name="email" placeholder="Digite seu e-mail" required>
            <button type="submit">ENVIAR LINK</button>
        </form>
        <a href="./cadastrar.php" class="back-link">Voltar para o login</a>
    </div>
           
    <div id="loading" style="display: none;">
        <p>Enviando e-mail, por favor aguarde...</p>
        <div class="spinner"></div>
    </div>

    <style>
    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border-left-color: #006400;
        animation: spin 1s linear infinite;
        margin: 10px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
        
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>

        <script type="text/javascript">
            emailjs.init("svEdyJuALIkI_XS3F");
        </script>

        <script>
        document.getElementById('contato-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = this.email.value;

            const senhaTemporaria = Math.random().toString(36).slice(-8); // exemplo de senha aleatória
   
            const templateParams = {
                email: email,
                link: senhaTemporaria
            };

            emailjs.send('service_mahe3va', 'template_ws4fezr', templateParams)
                .then(function(response) {
                    alert('Email enviado com sucesso!');
                    document.getElementById('contato-form').reset();
                    window.location.href = '../CONTROLLER/controller_recuperacao_senha.php?senhaTemp='+senhaTemporaria+'&email='+email;
                }, function(error) {
                    alert('Erro ao enviar email: ' + JSON.stringify(error));
                });
        });
        </script>

</body>
</html>