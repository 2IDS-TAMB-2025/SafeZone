// Animação de rolagem para o header
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 30) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Animação dos blocos de contato
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

// Animação de elementos na rolagem
function animeScroll() {
    const windowTop = window.pageYOffset + (window.innerHeight * 0.75);
    const elements = document.querySelectorAll('[data-anime]');
    
    elements.forEach(function(element) {
        if (windowTop > element.offsetTop) {
            element.classList.add('animate');
        } else {
            element.classList.remove('animate');
        }
    });
}

window.addEventListener('scroll', function() {
    animeScroll();
});

// Inicia as animações quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    animeScroll();
});

// Função para mostrar o mapa
function showMap(latitude, longitude, pontos){
    var map = L.map('map').setView([latitude, longitude], 13);
   
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'openstreetmap'
    }).addTo(map);
   
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Você está aqui!')
        .openPopup();
   
    for (var i = 0; i < pontos.length; i++) {
        L.marker([pontos[i]["lati"], pontos[i]["long"]]).addTo(map)
        .bindPopup(pontos[i]["local"])
        .openPopup();
    }
}

// Função para obter localização
function getLocation(){
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(
            function(position){
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                var pontos = [
                    {"local": "Zona Segura Acre: Jordão",
                    "lati": "-9.190885289471673",
                    "long": "-71.95037545063767"},
                   
                    {"local": "Zona Segura Alagoas: Flexeiras",
                    "lati": "-9.277225651526502",
                    "long": "-35.723561935035804"},

                    {"local": "Zona Segura Bahia: Piatã",
                    "lati": "-13.151124835434377",
                    "long": "-41.77586185798134"},
                   
                    {"local": "Zona Segura Amapá: Serra do Navio",
                    "lati": "0.9018802442680867",
                    "long": "-52.00287972395402"},

                    {"local": "Zona Segura Amazonas: Santa Isabel do Rio Negro",
                    "lati": "-0.4148546771374403",
                    "long": "-65.01591178601711"},

                    {"local": "Zona Segura Ceará: Quixadá",
                    "lati": "-4.968379861365324",
                    "long": "-39.01700414680048"},
                   
                    {"local": "Zona Segura Espiríto Santo: Ponto Belo",
                    "lati": "-18.12250578841321",
                    "long": "-40.538457086148846"},
                   
                    {"local": "Zona Segura Góias: Santo Antônio do Descoberto",
                    "lati": "-15.951383747314996",
                    "long": "-48.28055893578819"},

                    {"local": "Zona Segura Maranhão: Alcântara",
                    "lati": "-2.403951418303883",
                    "long": "-44.41565062016952"},

                    {"local": "Zona Segura Mato Grosso: Tangará da Serra",
                    "lati": "-14.620507094091675",
                    "long": "-57.48837121979413"},

                    {"local": "Zona Segura Mato Grosso do Sul:Maracaju",
                    "lati": "-21.630563111000367",
                    "long": "-55.15983364581099"},
                   
                    {"local": "Zona Segura Minas Gerais: Uberlândia",
                    "lati": "-18.914583700976312",
                    "long": "-48.28051746285704"},
                     
                    {"local": "Zona Segura Pará: Santa Izabel do Pará",
                    "lati": "-1.2975324518270486",
                    "long": "-48.163018194661326"},
                     
                    {"local": "Zona Segura Paraíba: Monteiro",
                    "lati": "-7.891702929352078",
                    "long": "-37.12456284353066"},

                    {"local": "Zona Segura Paraná: Maringá",
                    "lati": "-23.425949323032953",
                    "long": "-51.927373568411944"},

                    {"local": "Zona Segura Pernanbuco: Toritama",
                    "lati": "-8.007526529256486",
                    "long": "-36.060382739774376"},
                   
                    {"local": "Zona Segura Piauí: Altos",
                    "lati": "-5.040069381145025",
                    "long": "-42.4598231813796"},
                   
                    {"local": "Zona Segura Rio de Janeiro: Belford Roxo",
                    "lati": "-22.759841970548262",
                    "long": "-43.40234137732956"},

                    {"local": "Zona Segura  Rio Grande do Norte: Natal",
                    "lati": "-5.783820172078949",
                    "long": "-35.200239032073746"},
                   
                    {"local": "Zona Segura Rio Grande do Sul: Caxias do Sul",
                    "lati": "-29.1738775972273",
                    "long": "-51.19308223922427"},

                    {"local": "Zona Segura Rondônia: Porto Velho",
                    "lati": "-8.761287198449097",
                    "long": "-63.90023015847645"},

                    {"local": "Zona Segura Roraima: Boa Vista",
                    "lati": "2.8222840772152176",
                    "long": "-60.68043008800633"},

                    {"local": "Zona Segura Santa Catarina: Florianópolis",
                    "lati": "-27.589464850381376",
                    "long": "-48.54862454013866"},

                    {"local": "Zona Segura São Paulo: São Paulo",
                    "lati": "-23.553720971375103",
                    "long": "-46.61596888173541"},
                     
                    {"local": "Zona Segura  Sergipe: Aracaju",
                    "lati": "-10.924396199577323",
                    "long": "-37.071199789702234"},

                    {"local": "Zona Segura Tocantins: Palmas",
                    "lati": "-10.24856278976787",
                    "long": "-48.32183315132372"},
                   
                    {"local": "Zona Segura Distrito Federal: Brasília",
                    "lati": "-15.798804422472202",
                    "long": "-47.8989241084052"},
                ];
                showMap(latitude, longitude, pontos);
            }
        );  
    } else {
        alert("Localização não suportada");
    }
}

// Inicia o mapa
getLocation();

// Funções para os modais
function abrirModal(id) {
    document.getElementById(id).style.display = "block";
}

function fecharModal(id) {
    document.getElementById(id).style.display = "none";
}

window.onclick = function(event) {
    ['modal-privacidade','modal-termos','modal-faq'].forEach(function(id) {
        let modal = document.getElementById(id);
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
}