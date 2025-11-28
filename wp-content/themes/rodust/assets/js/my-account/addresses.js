/**
 * Addresses Module
 * Handles address CRUD operations, CEP search, and type toggles
 */
(function($) {
    'use strict';

    const Addresses = {
        addresses: [],
        editingAddressId: null,

        init: function() {
            console.log('[Addresses] Inicializando...');
            this.bindEvents();
            this.setupMasks();
            this.listenToTabChange();
        },

        listenToTabChange: function() {
            $(document).on('myaccount:tab-changed', (e, tab) => {
                console.log('[Addresses] Tab changed:', tab);
                if (tab === 'enderecos') {
                    this.loadAddresses();
                }
            });
        },

        bindEvents: function() {
            // New address button
            $('#btn-new-address').on('click', this.openNewAddressModal.bind(this));
            
            // Modal close
            $('#btn-cancel-address').on('click', this.closeModal.bind(this));
            
            // CEP search
            $('#search-zipcode').on('click', this.searchZipcode.bind(this));
            
            // Form submit
            $('#address-form').on('submit', this.handleSubmit.bind(this));
        },

        setupMasks: function() {
            // CEP mask
            $('#address-zipcode').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }
                $(this).val(value);
            });
        },

        openNewAddressModal: function() {
            this.editingAddressId = null;
            $('#modal-title').text('Novo Endereço');
            $('#address-form')[0].reset();
            $('#address-id').val('');
            $('#type-toggles').hide();
            $('#address-modal').removeClass('hidden');
        },

        closeModal: function() {
            $('#address-modal').addClass('hidden');
        },

        searchZipcode: function() {
            const zipcode = $('#address-zipcode').val().replace(/\D/g, '');
            
            if (zipcode.length !== 8) {
                window.MyAccount.showToast('error', 'Digite um CEP válido');
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
                    window.MyAccount.showToast('error', 'CEP não encontrado');
                }
            });
        },

        handleSubmit: function(e) {
            e.preventDefault();

            const zipcode = $('#address-zipcode').val().replace(/\D/g, '');
            const addressId = $('#address-id').val();
            const token = sessionStorage.getItem('customer_token');
            
            // Determine type based on checkboxes (only in edit mode)
            let type = null;
            if (addressId) {
                const isShipping = $('#is-shipping').is(':checked');
                const isBilling = $('#is-billing').is(':checked');
                
                // If both checked, prioritize shipping
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
                : window.RODUST_API_URL + '/api/customers/addresses';
            
            const method = addressId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify(data),
                success: (response) => {
                    this.closeModal();
                    this.loadAddresses();
                    window.MyAccount.showToast('success', response.message || 'Endereço salvo com sucesso!');
                    this.editingAddressId = null;
                },
                error: function(xhr) {
                    let errorMsg = 'Erro ao salvar endereço.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    window.MyAccount.showToast('error', errorMsg);
                }
            });
        },

        loadAddresses: function() {
            const token = sessionStorage.getItem('customer_token');
            console.log('[Addresses] Loading addresses...');

            $.ajax({
                url: window.RODUST_API_URL + '/api/customers/addresses',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: (response) => {
                    if (response.success) {
                        this.addresses = response.data.addresses;
                        this.renderAddresses();
                    }
                },
                error: function() {
                    $('#addresses-list').html('<p class="text-red-600">Erro ao carregar endereços.</p>');
                }
            });
        },

        renderAddresses: function() {
            const $list = $('#addresses-list');
            
            if (this.addresses.length === 0) {
                $list.html('<p class="text-gray-500 text-center py-8">Você ainda não cadastrou nenhum endereço.</p>');
                return;
            }

            let html = '';
            this.addresses.forEach((addr) => {
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
                                        onclick="Addresses.toggleAddressType(${addr.id}, 'shipping')"
                                        class="text-xs px-3 py-1.5 rounded font-medium transition-all ${
                                            isShipping 
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200 border-2 border-green-300' 
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border-2 border-gray-300'
                                        }">
                                        ${isShipping ? '🔘' : '◯'} Entrega
                                    </button>
                                    <button 
                                        onclick="Addresses.toggleAddressType(${addr.id}, 'billing')"
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
                                <p class="text-sm text-gray-600">CEP: ${this.formatZipcode(addr.zipcode)}</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="Addresses.editAddress(${addr.id})" class="text-gray-600 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="Addresses.deleteAddress(${addr.id})" class="text-gray-600 hover:text-red-600">
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
        },

        editAddress: function(id) {
            const addr = this.addresses.find(a => a.id === id);
            if (!addr) return;

            this.editingAddressId = id;
            $('#modal-title').text('Editar Endereço');
            
            $('#address-id').val(addr.id);
            $('#address-label').val(addr.label);
            $('#address-recipient').val(addr.recipient_name);
            $('#address-zipcode').val(this.formatZipcode(addr.zipcode));
            $('#address-street').val(addr.address);
            $('#address-number').val(addr.number);
            $('#address-complement').val(addr.complement);
            $('#address-neighborhood').val(addr.neighborhood);
            $('#address-city').val(addr.city);
            $('#address-state').val(addr.state);
            
            // Show type toggles only in edit mode
            $('#type-toggles').show();
            $('#is-shipping').prop('checked', addr.is_shipping === true || addr.is_shipping === 1);
            $('#is-billing').prop('checked', addr.is_billing === true || addr.is_billing === 1);

            $('#address-modal').removeClass('hidden');
        },

        toggleAddressType: function(id, type) {
            const token = sessionStorage.getItem('customer_token');

            $.ajax({
                url: window.RODUST_API_URL + '/api/customers/addresses/' + id + '/toggle-type',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({ type: type }),
                success: (response) => {
                    this.loadAddresses();
                    window.MyAccount.showToast('success', response.message || 'Endereço atualizado!');
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Erro ao atualizar endereço.';
                    window.MyAccount.showToast('error', errorMsg);
                }
            });
        },

        deleteAddress: function(id) {
            if (!confirm('Deseja realmente excluir este endereço?')) return;

            const token = sessionStorage.getItem('customer_token');

            $.ajax({
                url: window.RODUST_API_URL + '/api/customers/addresses/' + id,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: (response) => {
                    this.loadAddresses();
                    window.MyAccount.showToast('success', response.message || 'Endereço removido com sucesso!');
                },
                error: function() {
                    window.MyAccount.showToast('error', 'Erro ao excluir endereço.');
                }
            });
        },

        formatZipcode: function(zipcode) {
            return zipcode.replace(/(\d{5})(\d{3})/, '$1-$2');
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        Addresses.init();
    });

    // Expose to window for global access
    window.Addresses = Addresses;

})(jQuery);
