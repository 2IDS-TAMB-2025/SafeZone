// Header ao rolar
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 30) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Botão voltar ao topo
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

// Animação dos cards (sobre nós)
document.addEventListener('DOMContentLoaded', function() {
    const blocks = document.querySelectorAll('.blocks');
    
    function checkVisibility() {
        blocks.forEach((block, index) => {
            const blockPosition = block.getBoundingClientRect().top;
            const screenHeight = window.innerHeight;
            
            // se o topo do card estiver dentro da tela, aplica .visible
            if (blockPosition < screenHeight - 50) {
                setTimeout(() => {
                    block.classList.add('visible');
                }, index * 100);
            }
        });
    }

    // roda ao carregar e ao rolar
    checkVisibility();
    window.addEventListener('scroll', checkVisibility);
});

// Animação dos blocos de contato (conteudo2)
document.addEventListener('DOMContentLoaded', function() {
    const blocos = document.querySelectorAll('.conteudo2 .bloco');
    
    function animateBlocks() {
        const windowHeight = window.innerHeight;
        
        blocos.forEach(bloco => {
            const elementTop = bloco.getBoundingClientRect().top;
            
            if (elementTop < windowHeight - 100) {
                bloco.classList.add('visible');
            }
        });
    }
    
    animateBlocks();
    window.addEventListener('scroll', animateBlocks);
});
