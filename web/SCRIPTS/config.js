// Image upload and preview functionality
const inputImg = document.getElementById('input-img');
const previewImg = document.getElementById('preview-img-profile');
const resetBtn = document.getElementById('reset-img');
const defaultSrc = '../IMAGES/PERFIL/PERFIL_SEM_FOTO.png';

inputImg.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Por favor, selecione uma imagem JPEG, PNG ou GIF.');
            this.value = '';
            return;
        }
        
        // Validate file size (800KB max)
        if (file.size > 800 * 1024) {
            alert('A imagem deve ter no máximo 800KB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Reset image functionality
resetBtn.addEventListener('click', function () {
    if (confirm('Tem certeza que deseja redefinir a foto de perfil?')) {
        previewImg.src = defaultSrc;
        inputImg.value = '';
    }
});

// Edit/Save button functionality
$(document).ready(function () {
    $("#btn-editar").on("click", function () {
        // Enable all form controls
        $("input").not('[type="hidden"]').removeAttr("readonly");
        $("input[type='file']").removeAttr("disabled");
        $("select").removeAttr("disabled");
        
        // Toggle buttons
        $(this).hide();
        $("#btn-salvar").show();
        
        // Focus on first field
        $("input:not([readonly]):first").focus();
    });
    
    // Form validation before submission
    $("form").on("submit", function(e) {
        // Add any additional validation here if needed
        if ($("input[name='senha']").val().length < 6) {
            alert("A senha deve ter pelo menos 6 caracteres");
            e.preventDefault();
        }
    });
});

// Back to top button functionality
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

