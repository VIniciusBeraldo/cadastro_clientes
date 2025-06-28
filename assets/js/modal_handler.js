/**
 * Arquivo: modal_hander.js
 * Descrição: Contém funções para manipular o modal personalizado ao inves dos alert().
 * Autor: Vinicius Beraldo da Silva
 * 
 * function showCustomModal: Responsavel por tornar o modal visivel após ser chamada pelo window.showCustomModal.
 * function hideCustomModal: Responsavl por esconder o modal após os cliques nos botões de OK ou Close.
 */

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

        modalContent.classList.remove('modal-success', 'modal-error', 'modal-info');
        modalContent.classList.add(`modal-${type}`);

        modalContent.classList.remove('show');
        customModal.style.display = 'flex';

        setTimeout(() => {
            modalContent.classList.add('show');
        }, 10);

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
            if (callback) {
                callback();
            }
        };

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
        setTimeout(() => {
            customModal.style.display = 'none'; 
        }, 300); 
    }

    // Torna a função showCustomModal acessível globalmente
    window.showCustomModal = showCustomModal;
});