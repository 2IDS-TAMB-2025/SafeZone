document.addEventListener('DOMContentLoaded', function() {
    const solicitarForm = document.getElementById('solicitarForm');
    const verificarForm = document.getElementById('verificarForm');
    const novaSenhaForm = document.getElementById('novaSenhaForm');
    const mensagemDiv = document.getElementById('mensagem');
    let timer;

    function mostrarMensagem(texto, tipo) {
        mensagemDiv.textContent = texto;
        mensagemDiv.className = 'mensagem ' + tipo;
        mensagemDiv.style.display = 'block';
        
        if (tipo === 'sucesso') {
            setTimeout(() => {
                mensagemDiv.style.display = 'none';
            }, 5000);
        }
    }

    function iniciarContadorRegressivo(tempoSegundos) {
        if (timer) clearInterval(timer);
        
        const expiracaoElement = document.getElementById('expiracao');
        const agora = new Date();
        const dataExpiracao = new Date(agora.getTime() + tempoSegundos * 1000);
        
        function atualizarContador() {
            const agora = new Date();
            const diferenca = dataExpiracao - agora;
            
            if (diferenca <= 0) {
                clearInterval(timer);
                expiracaoElement.textContent = 'EXPIRADO';
                mostrarMensagem('O código expirou! Solicite um novo.', 'erro');
                return;
            }
            
            const segundos = Math.floor(diferenca / 1000) % 60;
            expiracaoElement.textContent = `00:${segundos.toString().padStart(2, '0')}`;
        }
        
        atualizarContador();
        timer = setInterval(atualizarContador, 1000);
    }

    solicitarForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        
        fetch('../CONTROLLER/controller_recuperacao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `acao=solicitar&email=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('solicitarContainer').style.display = 'none';
                document.getElementById('codigoContainer').style.display = 'block';
                document.getElementById('codigoDisplay').textContent = data.codigo;
                document.getElementById('emailVerificar').value = email;
                iniciarContadorRegressivo(60);
                mostrarMensagem('Código gerado com sucesso!', 'sucesso');
            } else {
                mostrarMensagem(data.mensagem, 'erro');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao processar solicitação', 'erro');
        });
    });

    verificarForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('emailVerificar').value;
        const codigo = document.getElementById('codigoInput').value;
        
        fetch('../CONTROLLER/controller_recuperacao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `acao=verificar&email=${encodeURIComponent(email)}&codigo=${codigo}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('codigoContainer').style.display = 'none';
                document.getElementById('novaSenhaContainer').style.display = 'block';
                document.getElementById('emailSenha').value = email;
                mostrarMensagem('Código verificado com sucesso!', 'sucesso');
                clearInterval(timer);
            } else {
                mostrarMensagem(data.mensagem, 'erro');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao verificar código', 'erro');
        });
    });

    novaSenhaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('emailSenha').value;
        const novaSenha = document.getElementById('novaSenha').value;
        const confirmarSenha = document.getElementById('confirmarSenha').value;
        
        if (novaSenha !== confirmarSenha) {
            mostrarMensagem('As senhas não coincidem!', 'erro');
            return;
        }
        
        if (novaSenha.length < 6) {
            mostrarMensagem('A senha deve ter pelo menos 6 caracteres', 'erro');
            return;
        }
        
        fetch('../CONTROLLER/controller_recuperacao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `acao=atualizar&email=${encodeURIComponent(email)}&novaSenha=${encodeURIComponent(novaSenha)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarMensagem('Senha atualizada com sucesso! Redirecionando para login...', 'sucesso');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                mostrarMensagem(data.mensagem, 'erro');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao atualizar senha', 'erro');
        });
    });
});