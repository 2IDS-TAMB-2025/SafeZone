window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 30) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
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
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    });

    document.querySelectorAll('.bloco2').forEach(bloco => {
        observer.observe(bloco);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const noticia = document.querySelector('.noticia');
    
    function checkVisibility() {
        const rect = noticia.getBoundingClientRect();
        const isVisible = (rect.top <= window.innerHeight * 0.75) && 
                         (rect.bottom >= window.innerHeight * 0.25);
        
        if (isVisible) {
            noticia.classList.add('visible');
            window.removeEventListener('scroll', checkVisibility);
        }
    }
    
    checkVisibility();
    
    window.addEventListener('scroll', checkVisibility);
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