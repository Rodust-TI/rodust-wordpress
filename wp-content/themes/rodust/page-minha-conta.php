<?php
/**
 * Template Name: Minha Conta
 */

get_header();
?>

<!-- Toast Notifications (fixed position) -->
<div id="toast-container" class="fixed top-4 right-4 z-50 w-full max-w-md space-y-2" style="pointer-events: none;">
    <!-- Toasts will be inserted here -->
</div>

<main class="container mx-auto px-4 py-12 md:py-16">
    
    <!-- Header da área do cliente -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Minha Conta</h1>
        <p class="text-gray-600" id="customer-welcome">Carregando...</p>
    </div>

    <!-- Mensagem de não autenticado (oculta por padrão) -->
    <div id="not-authenticated" class="hidden bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded mb-8">
        <p class="text-yellow-700 mb-4">Você precisa estar logado para acessar esta página.</p>
        <a href="<?php echo home_url('/login'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
            Fazer Login
        </a>
    </div>

    <!-- Conteúdo da área do cliente (oculto até carregar) -->
    <div id="customer-area" class="hidden">
        
        <!-- Navegação em Abas -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="flex space-x-8">
                <button class="tab-btn active py-4 px-2 border-b-2 border-blue-600 text-blue-600 font-medium" data-tab="dados">
                    Dados Pessoais
                </button>
                <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="enderecos">
                    Endereços
                </button>
                <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="pedidos">
                    Pedidos
                </button>
                <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="wishlist">
                    Lista de Desejos
                </button>
                <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-red-300 text-gray-600 hover:text-red-600 font-medium" onclick="logout()">
                    Sair
                </button>
            </nav>
        </div>

        <!-- Aba: Dados Pessoais -->
        <div id="tab-dados" class="tab-content">
            <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Dados Pessoais</h2>
                
                <div id="update-messages" class="mb-6 hidden"></div>

                <form id="update-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Nome Completo -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                            <input type="text" id="update-name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Tipo de Pessoa -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Pessoa *</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="person_type" id="person-type-f" value="F" checked class="form-radio text-blue-600">
                                    <span class="ml-2">Pessoa Física</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="person_type" id="person-type-j" value="J" class="form-radio text-blue-600">
                                    <span class="ml-2">Pessoa Jurídica</span>
                                </label>
                            </div>
                        </div>

                        <!-- CPF/CNPJ -->
                        <div id="field-cpf">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CPF *</label>
                            <input type="text" id="update-cpf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="000.000.000-00" maxlength="14">
                            <p class="text-xs text-gray-500 mt-1">Somente números</p>
                        </div>

                        <div id="field-cnpj" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CNPJ *</label>
                            <input type="text" id="update-cnpj" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="00.000.000/0000-00" maxlength="18">
                            <p class="text-xs text-gray-500 mt-1">Somente números</p>
                        </div>

                        <!-- Data de Nascimento (só PF) -->
                        <div id="field-birth-date">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                            <input type="date" id="update-birth-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Nome Fantasia (só PJ) -->
                        <div id="field-fantasy-name" class="hidden col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Fantasia</label>
                            <input type="text" id="update-fantasy-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Como a empresa é conhecida">
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefone Celular *</label>
                            <input type="tel" id="update-phone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="(00) 00000-0000">
                        </div>

                        <!-- Telefone Comercial -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefone Comercial</label>
                            <input type="tel" id="update-phone-commercial" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="(00) 0000-0000">
                        </div>

                        <!-- E-mail Principal -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-mail *</label>
                            <input type="email" id="update-email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- E-mail para NF-e -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-mail para NF-e (opcional)</label>
                            <input type="email" id="update-nfe-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deixe vazio para usar o e-mail principal">
                            <p class="text-xs text-gray-500 mt-1">E-mail que receberá as Notas Fiscais Eletrônicas</p>
                        </div>

                        <!-- Inscrição Estadual e Estado (só PJ) -->
                        <div id="field-state-registration" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Inscrição Estadual (IE) *</label>
                            <input type="text" id="update-state-registration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="535.371.914.110" maxlength="15">
                            <p class="text-xs text-gray-500 mt-1">12 dígitos (somente números)</p>
                        </div>

                        <div id="field-state-uf" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado (UF) *</label>
                            <select id="update-state-uf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecione...</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                            <p class="text-xs text-blue-600 mt-1">💡 Estado onde a empresa está registrada</p>
                        </div>

                        <!-- Alterar Senha -->
                        <div class="col-span-2 border-t pt-6 mt-4">
                            <h3 class="text-lg font-semibold mb-4">Alterar Senha</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                            <input type="password" id="update-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deixe em branco para manter">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha</label>
                            <input type="password" id="update-password-confirm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>

                    <div class="mt-8">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                            💾 Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aba: Endereços -->
        <div id="tab-enderecos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Meus Endereços</h2>
                    <button id="btn-new-address" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                        + Novo Endereço
                    </button>
                </div>

                <!-- Lista de Endereços -->
                <div id="addresses-list" class="space-y-4">
                    <p class="text-gray-500 text-center py-8">Carregando endereços...</p>
                </div>

                <!-- Modal: Adicionar/Editar Endereço -->
                <div id="address-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <h3 class="text-xl font-bold" id="modal-title">Novo Endereço</h3>
                        </div>
                        
                        <form id="address-form" class="p-6">
                            <input type="hidden" id="address-id">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- Label -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Identificação (opcional)</label>
                                    <input type="text" id="address-label" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Casa, Trabalho, Escritório">
                                </div>

                                <!-- Nome do Destinatário -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Destinatário</label>
                                    <input type="text" id="address-recipient" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Quem vai receber">
                                </div>

                                <!-- CEP -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CEP *</label>
                                    <input type="text" id="address-zipcode" maxlength="9" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="00000-000" required>
                                    <p class="text-xs text-blue-600 mt-1 cursor-pointer hover:underline" id="search-zipcode">Buscar endereço pelo CEP</p>
                                </div>

                                <div></div>

                                <!-- Logradouro -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logradouro *</label>
                                    <input type="text" id="address-street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>

                                <!-- Número -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Número *</label>
                                    <input type="text" id="address-number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>

                                <!-- Complemento -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                                    <input type="text" id="address-complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Bairro -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bairro *</label>
                                    <input type="text" id="address-neighborhood" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>

                                <!-- Cidade -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cidade *</label>
                                    <input type="text" id="address-city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                                    <select id="address-state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Selecione...</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                </div>

                                <!-- Usar como Entrega / Cobrança (apenas na edição) -->
                                <div class="col-span-2" id="type-toggles" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Usar este endereço como:</label>
                                    <div class="flex flex-col gap-2">
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" id="is-shipping" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">
                                                <span class="font-medium">📦 Endereço de Entrega</span>
                                                <span class="block text-xs text-gray-500 mt-1">Onde os produtos serão entregues</span>
                                            </span>
                                        </label>
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" id="is-billing" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">
                                                <span class="font-medium">💳 Endereço de Cobrança</span>
                                                <span class="block text-xs text-gray-500 mt-1">Para emissão de nota fiscal</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Campos de Faturamento (removidos) -->

                            </div>

                            <div class="flex gap-3 mt-6 pt-6 border-t">
                                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                                    Salvar Endereço
                                </button>
                                <button type="button" id="btn-cancel-address" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- Aba: Pedidos -->
        <div id="tab-pedidos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Meus Pedidos</h2>
                
                <!-- Loading -->
                <div id="orders-loading" class="text-center py-8">
                    <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-gray-600 mt-4">Carregando pedidos...</p>
                </div>
                
                <!-- Lista de Pedidos -->
                <div id="orders-list" class="hidden space-y-4"></div>
                
                <!-- Mensagem Vazia -->
                <div id="orders-empty" class="hidden text-center py-12">
                    <div class="text-6xl mb-4">📦</div>
                    <p class="text-gray-600 text-lg mb-2">Você ainda não possui pedidos</p>
                    <p class="text-gray-500 text-sm mb-6">Comece a explorar nossos produtos</p>
                    <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Ver Produtos
                    </a>
                </div>
                
                <!-- Modal: Detalhes do Pedido -->
                <div id="order-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b flex justify-between items-center">
                            <h3 class="text-xl font-bold" id="modal-order-title">Detalhes do Pedido</h3>
                            <button id="btn-close-order-details" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                        </div>
                        
                        <div id="order-details-content" class="p-6">
                            <!-- Conteúdo será inserido via JS -->
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <!-- Aba: Wishlist -->
        <div id="tab-wishlist" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Lista de Desejos</h2>
                <p class="text-gray-600">Sua lista de desejos está vazia.</p>
            </div>
        </div>

    </div>

</main>

<script>
jQuery(document).ready(function($) {
    
    const token = sessionStorage.getItem('customer_token');

    // Verificar autenticação
    if (!token) {
        $('#not-authenticated').removeClass('hidden');
        return;
    }

    // Carregar dados do cliente
    loadCustomerData();

    // Sistema de abas
    $('.tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        
        $('.tab-btn').removeClass('active border-blue-600 text-blue-600').addClass('border-transparent text-gray-600');
        $(this).removeClass('border-transparent text-gray-600').addClass('active border-blue-600 text-blue-600');
        
        $('.tab-content').addClass('hidden');
        $('#tab-' + tab).removeClass('hidden');
        
        // Carregar pedidos quando abrir a aba
        if (tab === 'pedidos' && !window.ordersLoaded) {
            loadOrders();
        }
    });
    
    // Verificar se deve abrir direto na aba de pedidos (via URL)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'pedidos') {
        $('.tab-btn[data-tab="pedidos"]').click();
    }

    // Update form
    $('#update-form').on('submit', function(e) {
        e.preventDefault();
        updateCustomerData();
    });

    // Toggle Pessoa Física / Jurídica
    $('input[name="person_type"]').on('change', function() {
        togglePersonType($(this).val());
    });

    function togglePersonType(type) {
        if (type === 'F') {
            // Pessoa Física
            $('#field-cpf').removeClass('hidden');
            $('#field-cnpj').addClass('hidden');
            $('#field-birth-date').removeClass('hidden');
            $('#field-fantasy-name').addClass('hidden');
            $('#field-state-registration').addClass('hidden');
            $('#field-state-uf').addClass('hidden');
        } else {
            // Pessoa Jurídica
            $('#field-cpf').addClass('hidden');
            $('#field-cnpj').removeClass('hidden');
            $('#field-birth-date').addClass('hidden');
            $('#field-fantasy-name').removeClass('hidden');
            $('#field-state-registration').removeClass('hidden');
            $('#field-state-uf').removeClass('hidden');
        }
    }

    // Máscaras
    $('#update-cpf').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        $(this).val(value);
    });

    $('#update-cnpj').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        }
        $(this).val(value);
    });

    // Máscara para Inscrição Estadual (12 dígitos)
    $('#update-state-registration').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 12) {
            // Formato: 535.371.914.110
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
        }
        $(this).val(value);
    });

    function loadCustomerData() {
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/me',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    const c = response.data;
                    
                    $('#customer-welcome').text('Bem-vindo(a), ' + c.name + '!');
                    $('#update-name').val(c.name);
                    $('#update-email').val(c.email);
                    $('#update-phone').val(c.phone || '');
                    $('#update-phone-commercial').val(c.phone_commercial || '');
                    $('#update-nfe-email').val(c.nfe_email || '');
                    $('#update-birth-date').val(c.birth_date || '');
                    $('#update-fantasy-name').val(c.fantasy_name || '');
                    $('#update-state-registration').val(c.state_registration ? formatStateRegistration(c.state_registration) : '');
                    $('#update-state-uf').val(c.state_uf || '');
                    
                    // Person type
                    const personType = c.person_type || 'F';
                    $('input[name="person_type"][value="' + personType + '"]').prop('checked', true);
                    togglePersonType(personType);
                    
                    // CPF e CNPJ separados
                    if (c.cpf) {
                        $('#update-cpf').val(formatCPF(c.cpf));
                    }
                    if (c.cnpj) {
                        $('#update-cnpj').val(formatCNPJ(c.cnpj));
                    }
                    
                    $('#customer-area').removeClass('hidden');
                } else {
                    $('#not-authenticated').removeClass('hidden');
                }
            },
            error: function() {
                sessionStorage.removeItem('customer_token');
                sessionStorage.removeItem('customer_data');
                $('#not-authenticated').removeClass('hidden');
            }
        });
    }

    function updateCustomerData() {
        const personType = $('input[name="person_type"]:checked').val();
        
        // Dados básicos
        const data = {
            name: $('#update-name').val(),
            email: $('#update-email').val(),
            phone: $('#update-phone').val(),
            person_type: personType,
            phone_commercial: $('#update-phone-commercial').val() || null,
            nfe_email: $('#update-nfe-email').val() || null,
            cpf: $('#update-cpf').val().replace(/\D/g, '') || null,
            cnpj: $('#update-cnpj').val().replace(/\D/g, '') || null,
        };

        // Campos específicos por tipo
        if (personType === 'F') {
            data.birth_date = $('#update-birth-date').val() || null;
        } else {
            data.fantasy_name = $('#update-fantasy-name').val() || null;
            data.state_registration = $('#update-state-registration').val().replace(/\D/g, '') || null;
            data.state_uf = $('#update-state-uf').val() || null;
        }

        // Senha
        const password = $('#update-password').val();
        const passwordConfirm = $('#update-password-confirm').val();

        if (password) {
            if (password !== passwordConfirm) {
                showUpdateMessage('error', 'As senhas não conferem.');
                return;
            }
            data.password = password;
            data.password_confirmation = passwordConfirm;
        }

        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/me',
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    showUpdateMessage('success', 'Dados atualizados com sucesso!');
                    $('#update-password').val('');
                    $('#update-password-confirm').val('');
                    
                    // Atualizar sessionStorage
                    sessionStorage.setItem('customer_data', JSON.stringify(response.data));
                    $('#customer-welcome').text('Bem-vindo(a), ' + response.data.name + '!');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Erro ao atualizar dados.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                showUpdateMessage('error', errorMsg);
            }
        });
    }

    function showUpdateMessage(type, message) {
        // Create toast notification
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? '✓' : '✕';
        
        const toast = $(`
            <div class="toast-item transform transition-all duration-300 translate-x-full opacity-0" style="pointer-events: auto;">
                <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                    <span class="text-2xl font-bold">${icon}</span>
                    <span class="flex-1">${message}</span>
                    <button class="toast-close text-white hover:text-gray-200 text-xl font-bold" onclick="$(this).closest('.toast-item').remove()">×</button>
                </div>
            </div>
        `);
        
        $('#toast-container').append(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.removeClass('translate-x-full opacity-0');
        }, 10);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.addClass('translate-x-full opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
        
        // Scroll to top smoothly
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    function formatCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return cpf;
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    function formatCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        if (cnpj.length !== 14) return cnpj;
        return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    function formatStateRegistration(ie) {
        ie = ie.replace(/\D/g, '');
        if (ie.length !== 12) return ie;
        return ie.replace(/(\d{3})(\d{3})(\d{3})(\d{3})/, '$1.$2.$3.$4');
    }

    window.logout = function() {
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/logout',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            complete: function() {
                sessionStorage.removeItem('customer_token');
                sessionStorage.removeItem('customer_data');
                window.location.href = '<?php echo home_url('/login'); ?>';
            }
        });
    };

    // ========================================
    // GERENCIAMENTO DE ENDEREÇOS
    // ========================================

    let addresses = [];
    let editingAddressId = null;

    // Carregar endereços quando abrir a aba
    $('[data-tab="enderecos"]').on('click', function() {
        loadAddresses();
    });

    // Abrir modal novo endereço
    $('#btn-new-address').on('click', function() {
        editingAddressId = null;
        $('#modal-title').text('Novo Endereço');
        $('#address-form')[0].reset();
        $('#address-id').val('');
        $('#type-toggles').hide(); // Esconder toggles ao criar novo
        $('#address-modal').removeClass('hidden');
    });

    // Fechar modal
    $('#btn-cancel-address').on('click', function() {
        $('#address-modal').addClass('hidden');
    });

    // Mostrar/ocultar campos de faturamento e checkbox padrão
    $('#address-type').on('change', function() {
        const type = $(this).val();
        
        // Remover campos de faturamento (não existem mais)
        $('#invoice-fields').addClass('hidden');
        
        // Mostrar checkbox de padrão apenas para shipping/billing
        if (type === 'shipping' || type === 'billing') {
            $('#default-checkbox-wrapper').removeClass('hidden');
        } else {
            $('#default-checkbox-wrapper').addClass('hidden');
            $('#address-default').prop('checked', false);
        }
    });

    // Máscara CEP
    $('#address-zipcode').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 8) {
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        $(this).val(value);
    });

    // Buscar CEP
    $('#search-zipcode').on('click', function() {
        const zipcode = $('#address-zipcode').val().replace(/\D/g, '');
        
        if (zipcode.length !== 8) {
            alert('Digite um CEP válido');
            return;
        }

        $.ajax({
            url: window.RODUST_API_URL + '/api/addresses/search-zipcode/' + zipcode,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#address-street').val(response.data.address);
                    $('#address-complement').val(response.data.complement);
                    $('#address-neighborhood').val(response.data.neighborhood);
                    $('#address-city').val(response.data.city);
                    $('#address-state').val(response.data.state);
                    $('#address-number').focus();
                }
            },
            error: function() {
                alert('CEP não encontrado');
            }
        });
    });

    // Submit do formulário
    $('#address-form').on('submit', function(e) {
        e.preventDefault();

        const zipcode = $('#address-zipcode').val().replace(/\D/g, '');
        const addressId = $('#address-id').val();
        
        // Determinar tipo baseado nos checkboxes (apenas na edição)
        let type = null;
        if (addressId) {
            const isShipping = $('#is-shipping').is(':checked');
            const isBilling = $('#is-billing').is(':checked');
            
            // Se ambos marcados, prioriza shipping
            if (isShipping) {
                type = 'shipping';
            } else if (isBilling) {
                type = 'billing';
            }
        }
        
        const data = {
            type: type,
            label: $('#address-label').val(),
            recipient_name: $('#address-recipient').val(),
            zipcode: zipcode,
            address: $('#address-street').val(),
            number: $('#address-number').val(),
            complement: $('#address-complement').val(),
            neighborhood: $('#address-neighborhood').val(),
            city: $('#address-city').val(),
            state: $('#address-state').val(),
        };

        const url = addressId
            ? window.RODUST_API_URL + '/api/customers/addresses/' + addressId
            : window.RODUST_API_URL + '/api/customers/addresses';        const method = addressId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify(data),
            success: function(response) {
                $('#address-modal').addClass('hidden');
                loadAddresses();
                showToast('success', response.message || 'Endereço salvo com sucesso!');
                editingAddressId = null;
            },
            error: function(xhr) {
                let errorMsg = 'Erro ao salvar endereço.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showToast('error', errorMsg);
            }
        });
    });

    function loadAddresses() {
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/addresses',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    addresses = response.data.addresses;
                    renderAddresses();
                }
            },
            error: function() {
                $('#addresses-list').html('<p class="text-red-600">Erro ao carregar endereços.</p>');
            }
        });
    }

    function renderAddresses() {
        const $list = $('#addresses-list');
        
        if (addresses.length === 0) {
            $list.html('<p class="text-gray-500 text-center py-8">Você ainda não cadastrou nenhum endereço.</p>');
            return;
        }

        let html = '';
        addresses.forEach(function(addr) {
            const isShipping = addr.is_shipping === true || addr.is_shipping === 1;
            const isBilling = addr.is_billing === true || addr.is_billing === 1;

            html += `
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="font-semibold text-gray-900">${addr.label || 'Endereço'}</span>
                            </div>
                            
                            <!-- Badges Clicáveis -->
                            <div class="flex gap-2 mb-3">
                                <button 
                                    onclick="toggleAddressType(${addr.id}, 'shipping')"
                                    class="text-xs px-3 py-1.5 rounded font-medium transition-all ${
                                        isShipping 
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200 border-2 border-green-300' 
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border-2 border-gray-300'
                                    }">
                                    ${isShipping ? '🔘' : '◯'} Entrega
                                </button>
                                <button 
                                    onclick="toggleAddressType(${addr.id}, 'billing')"
                                    class="text-xs px-3 py-1.5 rounded font-medium transition-all ${
                                        isBilling 
                                        ? 'bg-blue-100 text-blue-700 hover:bg-blue-200 border-2 border-blue-300' 
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border-2 border-gray-300'
                                    }">
                                    ${isBilling ? '🔘' : '◯'} Cobrança
                                </button>
                            </div>
                            
                            ${addr.recipient_name ? `<p class="text-sm text-gray-600">Para: ${addr.recipient_name}</p>` : ''}
                            <p class="text-sm text-gray-800">${addr.address}, ${addr.number}${addr.complement ? ' - ' + addr.complement : ''}</p>
                            <p class="text-sm text-gray-800">${addr.neighborhood} - ${addr.city}/${addr.state}</p>
                            <p class="text-sm text-gray-600">CEP: ${formatZipcode(addr.zipcode)}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editAddress(${addr.id})" class="text-gray-600 hover:text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteAddress(${addr.id})" class="text-gray-600 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        $list.html(html);
    }

    window.editAddress = function(id) {
        const addr = addresses.find(a => a.id === id);
        if (!addr) return;

        editingAddressId = id;
        $('#modal-title').text('Editar Endereço');
        
        $('#address-id').val(addr.id);
        $('#address-label').val(addr.label);
        $('#address-recipient').val(addr.recipient_name);
        $('#address-zipcode').val(formatZipcode(addr.zipcode));
        $('#address-street').val(addr.address);
        $('#address-number').val(addr.number);
        $('#address-complement').val(addr.complement);
        $('#address-neighborhood').val(addr.neighborhood);
        $('#address-city').val(addr.city);
        $('#address-state').val(addr.state);
        
        // Mostrar toggles de tipo apenas na edição
        $('#type-toggles').show();
        $('#is-shipping').prop('checked', addr.is_shipping === true || addr.is_shipping === 1);
        $('#is-billing').prop('checked', addr.is_billing === true || addr.is_billing === 1);

        $('#address-modal').removeClass('hidden');
    };

    window.toggleAddressType = function(id, type) {
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/addresses/' + id + '/toggle-type',
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ type: type }),
            success: function(response) {
                loadAddresses();
                showToast('success', response.message || 'Endereço atualizado!');
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Erro ao atualizar endereço.';
                showToast('error', errorMsg);
            }
        });
    };

    window.deleteAddress = function(id) {
        if (!confirm('Deseja realmente excluir este endereço?')) return;

        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/addresses/' + id,
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                loadAddresses();
                showToast('success', response.message || 'Endereço removido com sucesso!');
            },
            error: function() {
                showToast('error', 'Erro ao excluir endereço.');
            }
        });
    };

    function formatZipcode(zipcode) {
        return zipcode.replace(/(\d{5})(\d{3})/, '$1-$2');
    }
    
    // ========================================
    // FUNÇÕES DE PEDIDOS
    // ========================================
    
    function loadOrders() {
        if (window.ordersLoaded) return;
        
        $.ajax({
            url: window.RODUST_API_URL + '/api/orders',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                window.ordersLoaded = true;
                $('#orders-loading').addClass('hidden');
                
                if (response.data && response.data.length > 0) {
                    renderOrders(response.data);
                    $('#orders-list').removeClass('hidden');
                } else {
                    $('#orders-empty').removeClass('hidden');
                }
            },
            error: function() {
                $('#orders-loading').addClass('hidden');
                $('#orders-empty').removeClass('hidden');
                showToast('error', 'Erro ao carregar pedidos.');
            }
        });
    }
    
    function renderOrders(orders) {
        const $list = $('#orders-list');
        let html = '';
        
        orders.forEach(order => {
            const statusBadge = getStatusBadge(order.status);
            const date = new Date(order.created_at);
            const dateStr = date.toLocaleDateString('pt-BR');
            const timeStr = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            
            // Calcular total de itens (somando quantidades)
            const totalItems = order.items ? order.items.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0) : 0;
            const itemsCount = order.items ? order.items.length : 0;
            
            html += `
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="viewOrderDetails(${order.id})">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">Pedido #${order.order_number}</h3>
                            <p class="text-sm text-gray-600">${dateStr} às ${timeStr}</p>
                        </div>
                        <span class="${statusBadge.class}">${statusBadge.text}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Total</p>
                            <p class="font-semibold text-gray-900">R$ ${parseFloat(order.total_amount || order.total || 0).toFixed(2).replace('.', ',')}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Itens</p>
                            <p class="font-semibold text-gray-900">${totalItems} ${totalItems === 1 ? 'item' : 'itens'} (${itemsCount} ${itemsCount === 1 ? 'produto' : 'produtos'})</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Pagamento</p>
                            <p class="font-semibold text-gray-900">${getPaymentMethodLabel(order.payment_method)}</p>
                        </div>
                    </div>
                    
                    ${order.shipping_address && order.shipping_address.street ? `
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-500 uppercase mb-1">Entrega</p>
                        <p class="text-sm text-gray-700"> ${order.shipping_address.street}, ${order.shipping_address.number}</p>
                    </div>
                    ` : ''}
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            ${order.items && order.items.length > 0 ? order.items.slice(0, 2).map(item => item.product_name).join(', ') : 'Sem itens'}
                            ${order.items && order.items.length > 2 ? '...' : ''}
                        </p>
                        <span class="text-blue-600 font-semibold text-sm">Ver detalhes →</span>
                    </div>
                </div>
            `;
        });
        
        $list.html(html);
    }
    
    window.viewOrderDetails = function(orderId) {
        $.ajax({
            url: window.RODUST_API_URL + '/api/orders/' + orderId,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    showOrderDetailsModal(response.data);
                }
            },
            error: function() {
                showToast('error', 'Erro ao carregar detalhes do pedido.');
            }
        });
    };
    
    function showOrderDetailsModal(order) {
        const statusBadge = getStatusBadge(order.status);
        const date = new Date(order.created_at);
        const dateStr = date.toLocaleDateString('pt-BR');
        const timeStr = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        
        let itemsHtml = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const unitPrice = parseFloat(item.unit_price || 0);
                const totalPrice = parseFloat(item.total_price || 0);
                
                itemsHtml += `
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${item.product_name || 'Produto sem nome'}</p>
                            <p class="text-sm text-gray-600">Qtd: ${item.quantity} x R$ ${unitPrice.toFixed(2).replace('.', ',')}</p>
                        </div>
                        <p class="font-semibold text-gray-900">R$ ${totalPrice.toFixed(2).replace('.', ',')}</p>
                    </div>
                `;
            });
        } else {
            itemsHtml = '<p class="text-gray-500 text-center py-4">Nenhum item encontrado</p>';
        }
        
        // Verificar se shipping_address existe e tem zipcode
        let addressHtml = '';
        if (order.shipping_address && order.shipping_address.zipcode) {
            const addr = order.shipping_address;
            addressHtml = `
                <div>
                    <h5 class="font-semibold text-gray-900 mb-2">Endereço de Entrega</h5>
                    <p class="text-sm text-gray-800">${addr.street || ''}, ${addr.number || ''}${addr.complement ? ' - ' + addr.complement : ''}</p>
                    <p class="text-sm text-gray-800">${addr.neighborhood || ''} - ${addr.city || ''}/${addr.state || ''}</p>
                    <p class="text-sm text-gray-600">CEP: ${formatZipcode(addr.zipcode)}</p>
                </div>
            `;
        }
        
        const subtotal = parseFloat(order.subtotal_amount || order.subtotal || 0);
        const shipping = parseFloat(order.shipping_cost || order.shipping || 0);
        const total = parseFloat(order.total_amount || order.total || 0);
        
        const html = `
            <div class="space-y-6">
                <!-- Cabeçalho -->
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">Pedido #${order.order_number}</h4>
                        <p class="text-sm text-gray-600">${dateStr} às ${timeStr}</p>
                    </div>
                    <span class="${statusBadge.class}">${statusBadge.text}</span>
                </div>
                
                <!-- Itens -->
                <div>
                    <h5 class="font-semibold text-gray-900 mb-3">Itens do Pedido</h5>
                    <div class="space-y-2">
                        ${itemsHtml}
                    </div>
                </div>
                
                <!-- Totais -->
                <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Frete</span>
                        <span class="text-gray-900">R$ ${shipping.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">R$ ${total.toFixed(2).replace('.', ',')}</span>
                    </div>
                </div>
                
                <!-- Endereço de Entrega -->
                ${addressHtml}
                
                <!-- Informações de Pagamento -->
                <div>
                    <h5 class="font-semibold text-gray-900 mb-2">Pagamento</h5>
                    <p class="text-sm text-gray-800">${getPaymentMethodLabel(order.payment_method)}</p>
                    ${order.bling_id ? `<p class="text-xs text-gray-600 mt-1">ID Bling: ${order.bling_id}</p>` : ''}
                </div>
            </div>
        `;
        
        $('#modal-order-title').text('Pedido #' + order.order_number);
        $('#order-details-content').html(html);
        $('#order-details-modal').removeClass('hidden');
    }
    
    $('#btn-close-order-details').on('click', function() {
        $('#order-details-modal').addClass('hidden');
    });
    
    // Fechar modal ao clicar fora
    $('#order-details-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    function getStatusBadge(status) {
        const badges = {
            'pending': { text: 'Pendente', class: 'px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold' },
            'processing': { text: 'Processando', class: 'px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold' },
            'shipped': { text: 'Enviado', class: 'px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold' },
            'delivered': { text: 'Entregue', class: 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold' },
            'cancelled': { text: 'Cancelado', class: 'px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold' },
        };
        return badges[status] || { text: status, class: 'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold' };
    }
    
    function getPaymentMethodLabel(method) {
        const methods = {
            'pix': 'PIX',
            'boleto': 'Boleto Bancário',
            'credit_card': 'Cartão de Crédito',
            'debit_card': 'Cartão de Débito'
        };
        return methods[method] || method || 'Não informado';
    }
});
</script>

<?php get_footer(); ?>
