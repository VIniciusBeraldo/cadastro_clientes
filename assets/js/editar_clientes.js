/**
 * Arquivo: editar_clientes.js
 * Descrição: Preencher o formulario da tela 'Editar Cliente' com os dados obtidos atraves do id passado na url
 *            e ao clicar em 'Salvar Alterações' é enviado os dados para atualização no banco com a api atualizar_clientes.php.
 * Autor: Vinicius Beraldo da Silva
 * 
 */

document.addEventListener('DOMContentLoaded', function () {

    const formEdicaoCliente = document.getElementById('formEdicaoCliente');
    const urlParams = new URLSearchParams(window.location.search);
    const clienteID = urlParams.get('id');

    if (clienteID) {
        fetch(`api/clientes.php?id=${clienteID}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar dados do cliente: ' + response.statusText);
                }
                return response.json();
            })
            .then(cliente => {
                if (cliente.error) {
                    window.showCustomModal('Erro!', cliente.error, 'error');
                    return;
                }

                // Preenchendo o formulario de cliente com o retorno de clientes.php quando passado o id.
                document.getElementById('id').value = cliente.id;
                document.getElementById('nomeCompleto').value = cliente.nome_completo;
                document.getElementById('dataNascimento').value = cliente.data_nascimento;
                document.getElementById('cpf').value = cliente.cpf;
                document.getElementById('telefone').value = cliente.telefone;
                document.getElementById('email').value = cliente.email;
                document.getElementById('endereco').value = cliente.endereco;
            })
            .catch(error => {
                console.error('Erro ao carregar cliente para edição:', error);
                window.showCustomModal('Erro!', 'Não foi possível carregar os dados do cliente.', 'error');
            });
    } else {
        window.showCustomModal('Erro!', 'ID do cliente não fornecido na URL.', 'error');
    }

    // EventListener para quando clicar em Salvar Alterações.
    formEdicaoCliente.addEventListener('submit', function (event) {
        event.preventDefault(); // 

        const formData = new FormData(formEdicaoCliente);

        fetch('api/atualizar_cliente.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede ou no servidor ao atualizar cliente: ' + response.statusText);
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                window.showCustomModal('Sucesso!', data.message, 'success', () => {
                    window.location.href = 'index.html';
                });
            } else {
                window.showCustomModal('Erro!', data.message, 'error');
            }
        }).catch(error => {
            console.error('Erro ao enviar formulário:', error);
            window.showCustomModal('Erro Inesperado!', 'Erro ao tentar salvar as alterações: ' + error.message, 'error');
        });
    });
});