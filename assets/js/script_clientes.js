// script_clientes.js

/**
 * Arquivo: script_clientes.js
 * Descrição: Contém funções globais e manipuladores de eventos para o site.
 * Autor: Vinicius Beraldo da Silva
 * Data: 27/06/2025
 */

document.addEventListener('DOMContentLoaded', function () {

    const listaClientesDiv = document.getElementById('listaClientes');

    // Função responsavel para preencher a lista de clientes no momento em que a pagina for carregada.
    function fetchAndDisplayClients() {

        listaClientesDiv.innerHTML = '<p class="status-clientes">Carregando clientes...</p>';

        // Chamando a api_cliente para preencher a lista com os clientes encontrados e retornados na mesma.
        fetch('api/clientes.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede ou no servidor: ' + response.statusText);
                }
                return response.json();
            })
            .then(clientes => {
                if (clientes.error) {
                    listaClientesDiv.innerHTML = `<p class="status-clientes" style="color: red;">Erro ao carregar clientes: ${clientes.error}</p>`;
                    return;
                }

                if (clientes.length > 0) {
                    let tableHTML = `
                        <div class="container-tabela-cliente">
                            <table class="tabela-cliente">
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
                    clientes.forEach(cliente => {
                        tableHTML += `
                            <tr>
                                <td>${cliente.id}</td>
                                <td>${cliente.nome_completo}</td>
                                <td>${cliente.data_nascimento}</td>
                                <td>${cliente.cpf}</td>
                                <td>${cliente.telefone}</td>
                                <td>${cliente.email}</td>
                                <td>${cliente.endereco}</td>
                                <td>${cliente.data_cadastro}</td>
                                <td>
                                    <button class="action-button edit-button" data-id="${cliente.id}">Alterar</button>
                                    <button class="action-button delete-button" data-id="${cliente.id}">Excluir</button>
                                </td>
                            </tr>
                        `;
                    });
                    tableHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    listaClientesDiv.innerHTML = tableHTML;

                    addEventListenersToButtons();

                } else {
                    listaClientesDiv.innerHTML = '<p class="status-clientes">Nenhum cliente cadastrado ainda.</p>';
                }
            })
            .catch(error => {
                console.error('Houve um problema com a operação fetch:', error);
                listaClientesDiv.innerHTML = `<p class="status-clientes" style="color: red;">Não foi possível carregar os clientes. Tente novamente mais tarde.</p>`;
            });
    }

    // Função para adicionar os eventos de clique aos botões
    function addEventListenersToButtons() {

        
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                const clientId = this.dataset.id;

                if (confirm(`Tem certeza que deseja excluir o cliente com ID ${clientId}?`)) {
                    deleteClient(clientId);
                }
            });
        });

        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                const clientId = this.dataset.id;
                window.location.href = `editar_cliente.html?id=${clientId}`;
            });
        });
    }

    function deleteClient(id) {
        fetch('api/excluir_cliente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        }).then(response => {
                if (!response.ok) { 
                    throw new Error('Erro na rede ou no servidor: ' + response.statusText);
                }
                return response.json();
            }).then(data => {
                if (data.success) { 
                    window.showCustomModal('Sucesso!', data.message, 'success', () => {
                        fetchAndDisplayClients();
                    });
                } else {
                    window.showCustomModal('Erro!', data.message || 'Erro desconhecido ao excluir cliente.', 'error');
                }
            }).catch(error => { 
                console.error('Erro na requisição de exclusão:', error);
                window.showCustomModal('Erro!', 'Não foi possível excluir o cliente. Verifique o console do navegador (F12) para detalhes.', 'error');
            });
    }

    fetchAndDisplayClients();
});