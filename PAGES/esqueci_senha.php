<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Recuperar Senha</title>
    <link rel="stylesheet" href="../STYLES/esqueci_senha.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../IMAGES/LOGO2.png" alt="Safe Zone Logo">
        </div>
        
        <div class="form-container" id="solicitarContainer">
            <h2>Recuperar Senha</h2>
            <p>Digite seu email para gerar um código de verificação</p>
            <form id="solicitarForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn">Gerar Código</button>
            </form>
        </div>
        
        <div class="form-container" id="codigoContainer" style="display:none;">
            <h2>Seu Código de Verificação</h2>
            <div class="codigo-display" id="codigoDisplay"></div>
            <p class="expira-texto">Válido até: <span id="expiracao"></span></p>
            
            <form id="verificarForm">
                <input type="hidden" id="emailVerificar" name="email">
                <div class="form-group">
                    <label for="codigoInput">Digite o código acima:</label>
                    <input type="text" id="codigoInput" name="codigo" maxlength="6" required>
                </div>
                <button type="submit" class="btn">Verificar</button>
            </form>
        </div>
        
        <div class="form-container" id="novaSenhaContainer" style="display:none;">
            <h2>Crie uma Nova Senha</h2>
            <form id="novaSenhaForm">
                <input type="hidden" id="emailSenha" name="email">
                <div class="form-group">
                    <label for="novaSenha">Nova Senha:</label>
                    <input type="password" id="novaSenha" name="novaSenha" required>
                </div>
                <div class="form-group">
                    <label for="confirmarSenha">Confirmar Senha:</label>
                    <input type="password" id="confirmarSenha" name="confirmarSenha" required>
                </div>
                <button type="submit" class="btn">Atualizar Senha</button>
            </form>
        </div>
        
        <div id="mensagem" class="mensagem"></div>
    </div>

    <script src="../SCRIPTS/esqueci_senha.js"></script>
</body>
</html>