// assets/js/modal_handler.js

document.addEventListener('DOMContentLoaded', function() {
    const customModal = document.getElementById('customModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalButton = document.getElementById('modalButton');
    const closeModal = document.querySelector('.close-modal');
    const modalContent = document.querySelector('.modal-content');

    function showCustomModal(title, message, type = 'info', callback = null) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        // Limpa classes de tipo anteriores
        modalContent.classList.remove('modal-success', 'modal-error', 'modal-info');
        // Adiciona a classe de tipo
        modalContent.classList.add(`modal-${type}`);

        // Garante que o modal esteja oculto antes de aplicar a classe 'show'
        modalContent.classList.remove('show');
        customModal.style.display = 'flex'; // Exibe o overlay

        // Pequeno atraso para permitir que a transição CSS ocorra
        setTimeout(() => {
            modalContent.classList.add('show');
        }, 10);


        // Remove listeners anteriores para evitar múltiplos disparos
        modalButton.onclick = null;
        closeModal.onclick = null;

        modalButton.onclick = function() {
            hideCustomModal();
            if (callback) {
                callback();
            }
        };

        closeModal.onclick = function() {
            hideCustomModal();
            if (callback) { // Também chama o callback se fechar no X
                callback();
            }
        };

        // Permite fechar clicando fora do modal
        customModal.addEventListener('click', function(event) {
            if (event.target === customModal) {
                hideCustomModal();
                if (callback) {
                    callback();
                }
            }
        });
    }

    function hideCustomModal() {
        modalContent.classList.remove('show');
        // Atraso para a animação de saída do modal
        setTimeout(() => {
            customModal.style.display = 'none'; // Oculta o overlay após a animação
        }, 300); // Deve ser igual ou maior que a duração da transição do CSS
    }

    // Torna a função showCustomModal acessível globalmente
    window.showCustomModal = showCustomModal;
});