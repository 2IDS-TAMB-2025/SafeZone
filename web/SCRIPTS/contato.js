document.addEventListener('DOMContentLoaded', function() {
    // Configuração do formulário
    const contactForm = document.getElementById('contactForm');
    const submitBtn = contactForm?.querySelector('button[type="submit"]');
    
    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const originalBtnText = submitBtn.innerHTML;
            
            // Feedback visual
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            try {
                const formData = new FormData(contactForm);
                
                const response = await fetch('../CONTROLLER/controller_contato.php', {
                method: 'POST',
                body: formData
            });

                
                if (!response.ok) {
                    throw new Error('Erro na rede');
                }
                
                const data = await response.json();
                
                // Remove feedbacks anteriores
                const oldFeedback = document.querySelector('.form-feedback');
                if (oldFeedback) oldFeedback.remove();
                
                // Cria novo feedback
                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = `form-feedback ${data.success ? 'success' : 'error'}`;
                feedbackDiv.innerHTML = `
                    <i class="fas ${data.success ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${data.message}</span>
                `;
                
                // Insere antes do formulário
                contactForm.parentNode.insertBefore(feedbackDiv, contactForm);
                
                // Limpa formulário se sucesso
                if (data.success) {
                    contactForm.reset();
                }
                
                // Rolar até o feedback
                feedbackDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Remove o feedback após 5 segundos
                setTimeout(() => {
                    feedbackDiv.style.opacity = '0';
                    setTimeout(() => feedbackDiv.remove(), 300);
                }, 5000);
                
            } catch (error) {
                console.error('Erro:', error);
                alert('Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
    
    // Botão voltar ao topo
    const backToTopBtn = document.querySelector('.back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});