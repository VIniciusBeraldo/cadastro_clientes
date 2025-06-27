// script_clientes.js

document.addEventListener('DOMContentLoaded', function() {
    const clientsListDiv = document.getElementById('clientsList');

    // Função principal para buscar e exibir os clientes
    function fetchAndDisplayClients() {
        clientsListDiv.innerHTML = '<p class="no-clients-message">Carregando clientes...</p>';

        fetch('api/clientes.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede ou no servidor: ' + response.statusText);
                }
                return response.json();
            })
            .then(clients => {
                if (clients.error) {
                    clientsListDiv.innerHTML = `<p class="no-clients-message" style="color: red;">Erro ao carregar clientes: ${clients.error}</p>`;
                    return;
                }

                if (clients.length > 0) {
                    let tableHTML = `
                        <div class="client-table-container">
                            <table class="client-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome Completo</th>
                                        <th>Data Nasc.</th>
                                        <th>CPF</th>
                                        <th>Telefone</th>
                                        <th>Email</th>
                                        <th>Endereço</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    clients.forEach(client => {
                        tableHTML += `
                            <tr>
                                <td>${client.id}</td>
                                <td>${client.nome_completo}</td>
                                <td>${client.data_nascimento}</td>
                                <td>${client.cpf}</td>
                                <td>${client.telefone}</td>
                                <td>${client.email}</td>
                                <td>${client.endereco}</td>
                                <td>${client.data_cadastro}</td>
                                <td>
                                    <button class="action-button edit-button" data-id="${client.id}">Alterar</button>
                                    <button class="action-button delete-button" data-id="${client.id}">Excluir</button>
                                </td>
                            </tr>
                        `;
                    });
                    tableHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    clientsListDiv.innerHTML = tableHTML;

                    // IMPORTANTE: ADICIONAR OS EVENT LISTENERS APÓS O HTML SER CARREGADO
                    addEventListenersToButtons();

                } else {
                    clientsListDiv.innerHTML = '<p class="no-clients-message">Nenhum cliente cadastrado ainda.</p>';
                }
            })
            .catch(error => {
                console.error('Houve um problema com a operação fetch:', error);
                clientsListDiv.innerHTML = `<p class="no-clients-message" style="color: red;">Não foi possível carregar os clientes. Tente novamente mais tarde.</p>`;
            });
    }

    // Função para adicionar os eventos de clique aos botões
    function addEventListenersToButtons() {
        // Seleciona todos os botões de exclusão
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const clientId = this.dataset.id; // Pega o ID do atributo data-id
                // Substitui confirm() por um modal de confirmação se desejar um mais estilizado
                // Por simplicidade, mantemos o confirm() aqui
                if (confirm(`Tem certeza que deseja excluir o cliente com ID ${clientId}?`)) {
                    deleteClient(clientId); // Chama a função para excluir
                }
            });
        });

        // Seleciona todos os botões de alteração
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                const clientId = this.dataset.id; // Pega o ID do atributo data-id
                // Redireciona para a página de edição com o ID do cliente
                window.location.href = `editar_cliente.html?id=${clientId}`;
            });
        });
    }

    // Função para EXCLUIR cliente (envia uma requisição ao PHP)
    function deleteClient(id) {
        fetch('api/excluir_cliente.php', {
            method: 'POST', // Usamos POST para enviar dados
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Tipo de conteúdo para enviar formulários
            },
            body: `id=${id}` // O dado que será enviado: o ID do cliente
        })
        .then(response => {
            if (!response.ok) { // Verifica se a resposta HTTP foi bem-sucedida (código 200)
                throw new Error('Erro na rede ou no servidor: ' + response.statusText);
            }
            return response.json(); // Espera que a resposta do PHP seja JSON
        })
        .then(data => {
            if (data.success) { // Verifica se a operação foi um sucesso (conforme o JSON do PHP)
                window.showCustomModal('Sucesso!', data.message, 'success', () => { // Substitui alert()
                    fetchAndDisplayClients(); // Recarrega a lista para mostrar a remoção
                });
            } else {
                window.showCustomModal('Erro!', data.message || 'Erro desconhecido ao excluir cliente.', 'error'); // Substitui alert()
            }
        })
        .catch(error => { // Captura erros na requisição ou processamento
            console.error('Erro na requisição de exclusão:', error);
            window.showCustomModal('Erro!', 'Não foi possível excluir o cliente. Verifique o console do navegador (F12) para detalhes.', 'error'); // Substitui alert()
        });
    }

    // Inicia o carregamento dos clientes quando a página está pronta
    fetchAndDisplayClients();
});