window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 30) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

function abrirModalAdicionar() {
            document.getElementById('tituloModal').innerText = 'Adicionar Sensor';
            document.getElementById('acao').value = 'adicionar';
            document.getElementById('id_sensor').value = '';
            document.getElementById('tipo').value = '';
            document.getElementById('localizacao').value = '';
            document.getElementById('data_instalacao').value = '';
            document.getElementById('status').value = 'Ativo';
            document.getElementById('modalSensor').style.display = 'block';
        }

        function abrirModalEditar(sensor) {
            document.getElementById('tituloModal').innerText = 'Editar Sensor';
            document.getElementById('acao').value = 'editar';
            document.getElementById('id_sensor').value = sensor.ID_SENSOR;
            document.getElementById('tipo').value = sensor.TIPO_SENSOR;
            document.getElementById('localizacao').value = sensor.LOCALIZACAO;
            document.getElementById('data_instalacao').value = sensor.DATA_INSTALACAO;
            document.getElementById('status').value = sensor.STATUS_SENSOR;
            document.getElementById('modalSensor').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('modalSensor').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalSensor');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            const backToTopBtn = document.querySelector('.back-to-top');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopBtn.style.display = 'flex';
            } else {
                backToTopBtn.style.display = 'none';
            }
        }

        document.querySelector('.back-to-top').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

document.addEventListener('DOMContentLoaded', function() {
    const blocos = document.querySelectorAll('.conteudo2 .bloco');
    
    function animateBlocks() {
        const windowHeight = window.innerHeight;
        
        blocos.forEach(bloco => {
            const elementTop = bloco.getBoundingClientRect().top;
            const elementVisible = 100;
            
            if (elementTop < windowHeight - elementVisible) {
                bloco.classList.add('visible');
            }
        });
    }
    
    animateBlocks();
    
    window.addEventListener('scroll', animateBlocks);
});

document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.querySelector('.back-to-top');  
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });

    backToTopButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

function atualizarStatusSensor(id, novoStatus) {
    fetch('../CONTROLLER/controller_SENSORES.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `acao=editar-status&id=${id}&status=${novoStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.mensagem) {
            alert(data.mensagem);
            location.reload();
        } else if (data.erro) {
            alert(data.erro);
        }
    })
    .catch(error => console.error('Erro:', error));
}

function confirmarAlterarStatus(id, statusAtual) {
    const novoStatus = prompt(`Alterar status do sensor ${id} (Atual: ${statusAtual}).\nDigite o novo status (Ativo/Inativo/Manutenção):`);
    
    if (novoStatus && ['Ativo', 'Inativo', 'Manutenção'].includes(novoStatus)) {
        if (confirm(`Confirmar alteração do sensor ${id} para ${novoStatus}?`)) {
            atualizarStatusSensor(id, novoStatus);
        }
    } else if (novoStatus) {
        alert('Status inválido! Use apenas: Ativo, Inativo ou Manutenção');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-status').forEach(btn => {
        btn.addEventListener('click', function() {
            const sensorId = this.getAttribute('data-id');
            const sensorStatus = this.getAttribute('data-status');
            confirmarAlterarStatus(sensorId, sensorStatus);
        });
    });
});

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este sensor?')) {
        fetch(`../CONTROLLER/controller_SENSORES.php?acao=excluir&id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede');
                }
                return response.json();
            })
            .then(data => {
                if (data.mensagem) {
                    // alert(data.mensagem);
                    document.querySelector(`.sensor-card[data-id="${id}"]`)?.remove();
                } else if (data.erro) {
                    alert(data.erro);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao excluir sensor: ' + error.message);
            });
    }
}