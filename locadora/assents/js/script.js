document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('container');
    const signUpBtn = document.getElementById('signUp');
    const signInBtn = document.getElementById('signIn');
    const overlayBtn = document.getElementById('overlayBtn');

    // Adiciona evento de clique para o botão de cadastro
    signUpBtn.addEventListener('click', function() {
        container.classList.add('right-panel-active');
    });

    // Adiciona evento de clique para o botão de login
    signInBtn.addEventListener('click', function() {
        container.classList.remove('right-panel-active');
    });

    // Se quiser manter o overlayBtn também funcionando
    if (overlayBtn) {
        overlayBtn.addEventListener('click', function() {
            container.classList.toggle('right-panel-active');
            
            // Efeito de clique
            this.style.transform = 'translateX(-50%) scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'translateX(-50%) scale(1.05)';
            }, 200);
        });
    }
});

