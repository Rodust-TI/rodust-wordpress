/**
 * Personal Data Module
 * Handles customer profile management, person type toggle, and password updates
 */
(function($) {
    'use strict';

    const PersonalData = {
        init: function() {
            this.bindEvents();
            this.setupMasks();
            this.listenToAccountReady();
        },

        listenToAccountReady: function() {
            $(document).on('myaccount:loaded', (e, customerData) => {
                console.log('[PersonalData] Recebido evento myaccount:loaded', customerData);
                this.populateForm(customerData);
            });
        },

        bindEvents: function() {
            // Person type toggle
            $('input[name="person_type"]').on('change', this.togglePersonType.bind(this));
            
            // Form submission
            $('#personal-data-form').on('submit', this.handleSubmit.bind(this));
        },

        setupMasks: function() {
            const self = this;
            
            // CPF mask
            $('#update-cpf').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                }
                $(this).val(value);
            });

            // CNPJ mask
            $('#update-cnpj').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length <= 14) {
                    value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                }
                $(this).val(value);
            });

            // Phone masks
            $('#update-phone, #update-phone-commercial').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
                }
                $(this).val(value);
            });

            // State Registration mask
            $('#update-state-registration').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length <= 12) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{3})/, '$1.$2.$3.$4');
                }
                $(this).val(value);
            });
        },

        togglePersonType: function(e) {
            const personType = $(e.target).val();
            
            if (personType === 'F') {
                // Pessoa Física
                $('#pf-fields').removeClass('hidden');
                $('#pj-fields').addClass('hidden');
            } else {
                // Pessoa Jurídica
                $('#pf-fields').addClass('hidden');
                $('#pj-fields').removeClass('hidden');
            }
        },

        populateForm: function(customer) {
            if (!customer) return;

            // Basic fields
            $('#update-name').val(customer.name || '');
            $('#update-email').val(customer.email || '');
            $('#update-phone').val(customer.phone || '');
            $('#update-phone-commercial').val(customer.phone_commercial || '');
            $('#update-nfe-email').val(customer.nfe_email || '');

            // Person type
            const personType = customer.person_type || 'F';
            $(`input[name="person_type"][value="${personType}"]`).prop('checked', true).trigger('change');

            // PF fields
            if (personType === 'F') {
                $('#update-cpf').val(customer.cpf ? this.formatCPF(customer.cpf) : '');
                $('#update-birth-date').val(customer.birth_date || '');
            }

            // PJ fields
            if (personType === 'J') {
                $('#update-cnpj').val(customer.cnpj ? this.formatCNPJ(customer.cnpj) : '');
                $('#update-fantasy-name').val(customer.fantasy_name || '');
                $('#update-state-registration').val(customer.state_registration || '');
                $('#update-state-uf').val(customer.state_uf || '');
            }
        },

        handleSubmit: function(e) {
            e.preventDefault();

            const personType = $('input[name="person_type"]:checked').val();
            const token = sessionStorage.getItem('customer_token');
            
            // Build data object
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

            // Type-specific fields
            if (personType === 'F') {
                data.birth_date = $('#update-birth-date').val() || null;
            } else {
                data.fantasy_name = $('#update-fantasy-name').val() || null;
                data.state_registration = $('#update-state-registration').val().replace(/\D/g, '') || null;
                data.state_uf = $('#update-state-uf').val() || null;
            }

            // Password validation
            const password = $('#update-password').val();
            const passwordConfirm = $('#update-password-confirm').val();

            if (password) {
                if (password !== passwordConfirm) {
                    window.MyAccount.showToast('error', 'As senhas não conferem.');
                    return;
                }
                data.password = password;
                data.password_confirmation = passwordConfirm;
            }

            // Submit
            this.updateCustomerData(data, token);
        },

        updateCustomerData: function(data, token) {
            $.ajax({
                url: window.RODUST_API_URL + '/api/customers/me',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify(data),
                success: (response) => {
                    if (response.success) {
                        window.MyAccount.showToast('success', 'Dados atualizados com sucesso!');
                        
                        // Clear password fields
                        $('#update-password').val('');
                        $('#update-password-confirm').val('');
                        
                        // Update sessionStorage
                        sessionStorage.setItem('customer_data', JSON.stringify(response.data));
                        $('#customer-welcome').text('Bem-vindo(a), ' + response.data.name + '!');
                    }
                },
                error: (xhr) => {
                    let errorMsg = 'Erro ao atualizar dados.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    window.MyAccount.showToast('error', errorMsg);
                }
            });
        },

        formatCPF: function(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11) return cpf;
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        },

        formatCNPJ: function(cnpj) {
            cnpj = cnpj.replace(/\D/g, '');
            if (cnpj.length !== 14) return cnpj;
            return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        PersonalData.init();
    });

    // Expose to window for global access if needed
    window.PersonalData = PersonalData;

})(jQuery);
