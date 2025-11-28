/**
 * My Account - Main Module
 * 
 * Gerencia autenticação, navegação de abas e funções compartilhadas
 */

const MyAccount = (function($) {
    'use strict';
    
    let token = null;
    let customerData = null;
    
    /**
     * Inicialização
     */
    function init() {
        console.log('[MyAccount] Inicializando...');
        console.log('[MyAccount] API URL:', window.RODUST_API_URL);
        
        token = sessionStorage.getItem('customer_token');
        console.log('[MyAccount] Token:', token ? 'Encontrado' : 'NÃO ENCONTRADO');
        
        if (!token) {
            showNotAuthenticated();
            return;
        }
        
        loadCustomerData();
        initTabNavigation();
    }
    
    /**
     * Carregar dados do cliente
     */
    function loadCustomerData() {
        const apiUrl = window.RODUST_API_URL + '/api/customers/me';
        console.log('[MyAccount] Carregando dados do cliente de:', apiUrl);
        
        $.ajax({
            url: apiUrl,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('[MyAccount] Dados recebidos:', response);
                if (response.success) {
                    customerData = response.data;
                    $('#customer-welcome').text('Bem-vindo(a), ' + customerData.name + '!');
                    $('#customer-area').removeClass('hidden');
                    
                    // Disparar evento customizado
                    $(document).trigger('myaccount:loaded', [customerData]);
                    
                    // Verificar se tem parâmetro tab na URL (após carregar dados)
                    checkUrlTab();
                } else {
                    console.error('[MyAccount] Response não indica sucesso:', response);
                    showNotAuthenticated();
                }
            },
            error: function(xhr, status, error) {
                console.error('[MyAccount] Erro ao carregar dados:');
                console.error('  Status:', status);
                console.error('  Error:', error);
                console.error('  Response:', xhr.responseText);
                console.error('  Status Code:', xhr.status);
                
                sessionStorage.removeItem('customer_token');
                sessionStorage.removeItem('customer_data');
                showNotAuthenticated();
            }
        });
    }
    
    /**
     * Sistema de navegação de abas
     */
    function initTabNavigation() {
        $('.tab-btn').on('click', function() {
            const tab = $(this).data('tab');
            switchTab(tab);
        });
    }
    
    /**
     * Trocar de aba
     */
    function switchTab(tab) {
        // Atualizar botões
        $('.tab-btn').removeClass('active border-blue-600 text-blue-600')
                     .addClass('border-transparent text-gray-600');
        $(`.tab-btn[data-tab="${tab}"]`)
            .removeClass('border-transparent text-gray-600')
            .addClass('active border-blue-600 text-blue-600');
        
        // Atualizar conteúdo
        $('.tab-content').addClass('hidden');
        $('#tab-' + tab).removeClass('hidden');
        
        // Atualizar URL sem recarregar página (opcional, mas melhora UX)
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
        
        // Disparar evento customizado para a aba
        $(document).trigger('myaccount:tab-changed', [tab]);
    }
    
    /**
     * Verificar se URL tem parâmetro de aba
     */
    function checkUrlTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        console.log('[MyAccount] Verificando URL tab:', tab);
        
        if (tab) {
            console.log('[MyAccount] Trocando para aba:', tab);
            switchTab(tab);
        }
    }
    
    /**
     * Mostrar mensagem de não autenticado
     */
    function showNotAuthenticated() {
        $('#not-authenticated').removeClass('hidden');
    }
    
    /**
     * Logout
     */
    function logout() {
        sessionStorage.removeItem('customer_token');
        sessionStorage.removeItem('customer_data');
        window.location.href = window.RODUST_HOME_URL + '/login';
    }
    
    /**
     * Toast notification
     */
    function showToast(type, message) {
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
        
        setTimeout(() => toast.removeClass('translate-x-full opacity-0'), 10);
        setTimeout(() => {
            toast.addClass('translate-x-full opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    /**
     * Helpers
     */
    function formatCPF(cpf) {
        if (!cpf) return '';
        cpf = cpf.replace(/\D/g, '');
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }
    
    function formatCNPJ(cnpj) {
        if (!cnpj) return '';
        cnpj = cnpj.replace(/\D/g, '');
        return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    
    function formatZipcode(zipcode) {
        if (!zipcode) return '';
        zipcode = zipcode.replace(/\D/g, '');
        return zipcode.replace(/(\d{5})(\d{3})/, '$1-$2');
    }
    
    // Public API
    return {
        init,
        getToken: () => token,
        getCustomerData: () => customerData,
        switchTab,
        logout,
        showToast,
        formatCPF,
        formatCNPJ,
        formatZipcode
    };
    
})(jQuery);

// Inicializar quando documento estiver pronto
jQuery(document).ready(function() {
    MyAccount.init();
});
