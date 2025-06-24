window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 30) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
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

document.addEventListener('DOMContentLoaded', function() {
        const blocks = document.querySelectorAll('.blocks');
        
        function checkVisibility() {
            blocks.forEach((block, index) => {
                const blockPosition = block.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (blockPosition < screenPosition) {
                    setTimeout(() => {
                        block.classList.add('visible');
                    }, index * 100);
                }
            });
        }

        checkVisibility();

        window.addEventListener('scroll', checkVisibility);
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