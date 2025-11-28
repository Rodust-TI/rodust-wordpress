/**
 * Mercado Pago - Card Payment Integration
 * 
 * @package RodustEcommerce
 */

(function($) {
    'use strict';
    
    let mpInstance = null;
    let cardFormInstance = null;
    let publicKey = null;
    
    /**
     * Initialize Mercado Pago SDK
     */
    async function initMercadoPago() {
        try {
            // Buscar public key da API
            const response = await $.ajax({
                url: RODUST_PAYMENT.api_url + '/api/payments/mercadopago/public-key',
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (response.success && response.public_key) {
                publicKey = response.public_key;
                
                // Inicializar Mercado Pago
                mpInstance = new MercadoPago(publicKey);
                
                console.log('Mercado Pago SDK inicializado');
                setupCardForm();
            } else {
                throw new Error('Public key não encontrada');
            }
        } catch (error) {
            console.error('Erro ao inicializar Mercado Pago:', error);
            showError('Erro ao carregar sistema de pagamento. Tente novamente.');
        }
    }
    
    /**
     * Setup card form with Mercado Pago
     */
    function setupCardForm() {
        try {
            cardFormInstance = mpInstance.cardForm({
                amount: getOrderTotal(),
                iframe: false,
                form: {
                    id: "mp-card-form",
                    cardNumber: {
                        id: "cardNumber",
                        placeholder: "0000 0000 0000 0000",
                    },
                    expirationDate: {
                        id: "cardExpirationDate",
                        placeholder: "MM/AA",
                    },
                    securityCode: {
                        id: "securityCode",
                        placeholder: "123",
                    },
                    cardholderName: {
                        id: "cardholderName",
                        placeholder: "Nome como está no cartão",
                    },
                    issuer: {
                        id: "issuerInput",
                        placeholder: "Banco emissor",
                    },
                    installments: {
                        id: "installments",
                        placeholder: "Parcelas",
                    },
                    identificationType: {
                        id: "identificationType",
                    },
                    identificationNumber: {
                        id: "cardholderDocument",
                        placeholder: "000.000.000-00",
                    },
                },
                callbacks: {
                    onFormMounted: error => {
                        if (error) {
                            console.error('Erro ao montar formulário:', error);
                            showError('Erro ao carregar formulário de pagamento');
                        } else {
                            console.log('Formulário de cartão montado com sucesso');
                            applyBrazilianMasks();
                        }
                    },
                    onSubmit: event => {
                        event.preventDefault();
                        return false; // Prevenir submit padrão
                    },
                    onFetching: (resource) => {
                        console.log("Buscando:", resource);
                        
                        // Mostrar loading quando buscar parcelas
                        if (resource === 'installments') {
                            $('#installments').html('<option value="">Carregando parcelas...</option>');
                        }
                        
                        return () => {
                            console.log('Concluído:', resource);
                            
                            // Mostrar issuer select se tiver múltiplas opções
                            if (resource === 'issuer') {
                                const issuerSelect = document.getElementById('issuerInput');
                                const issuerGroup = document.getElementById('issuerGroup');
                                if (issuerSelect && issuerSelect.options.length > 1) {
                                    issuerGroup.style.display = 'block';
                                }
                            }
                        };
                    }
                },
            });
            
            // Store for later use
            window.RodustMPCardForm = cardFormInstance;
            
        } catch (error) {
            console.error('Erro ao setup card form:', error);
            showError('Erro ao configurar formulário de pagamento');
        }
    }
    
    /**
     * Apply Brazilian masks to inputs
     */
    function applyBrazilianMasks() {
        // CPF mask
        const cpfInput = $('#cardholderDocument');
        
        cpfInput.on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            $(this).val(value);
        });
        
        // Remover formatação antes do SDK processar
        cpfInput.on('blur', function() {
            const cleanValue = $(this).val().replace(/\D/g, '');
            $(this).attr('data-clean-value', cleanValue);
        });
        
        // Expiration date mask (já formatado pelo MP, mas garantir)
        $('#cardExpirationDate').on('blur', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length >= 4) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
                $(this).val(value);
            }
        });
    }
    
    /**
     * Get order total from checkout data
     */
    function getOrderTotal() {
        const checkoutData = sessionStorage.getItem('checkout_data');
        if (!checkoutData) return "0";
        
        try {
            const data = JSON.parse(checkoutData);
            const subtotal = data.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shipping = parseFloat(data.shipping.price);
            const total = subtotal + shipping;
            return total.toFixed(2);
        } catch (e) {
            return "0";
        }
    }
    function getOrderTotal() {
        const checkoutData = sessionStorage.getItem('checkout_data');
        if (!checkoutData) return "0";
        
        try {
            const data = JSON.parse(checkoutData);
            const subtotal = data.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shipping = parseFloat(data.shipping.price);
            const total = subtotal + shipping;
            return total.toFixed(2);
        } catch (e) {
            return "0";
        }
    }
    
    /**
     * Create card payment (called from main payment.js)
     */
    window.processCardPayment = async function(orderData) {
        try {
            // Validate form
            const form = document.getElementById('mp-card-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return null;
            }
            
            // Validate required fields
            const cardNumber = $('#cardNumber').val();
            const cardholderName = $('#cardholderName').val();
            const expirationDate = $('#cardExpirationDate').val();
            const securityCode = $('#securityCode').val();
            const cardholderDocument = $('#cardholderDocument').val().replace(/\D/g, '');
            const installments = $('#installments').val();
            
            if (!cardNumber || !cardholderName || !expirationDate || !securityCode || !cardholderDocument || !installments) {
                showError('Preencha todos os campos do cartão');
                return null;
            }
            
            if (cardholderDocument.length !== 11) {
                showError('CPF inválido');
                return null;
            }
            
            showLoading();
            hideError();
            
            console.log('Criando token do cartão...');
            
            // Limpar CPF antes de criar token
            const cleanCPF = cardholderDocument.replace(/\D/g, '');
            $('#cardholderDocument').val(cleanCPF);
            
            // Get card form data and create token
            const tokenData = await cardFormInstance.createCardToken();
            
            if (!tokenData || !tokenData.token) {
                throw new Error('Não foi possível gerar o token do cartão');
            }
            
            console.log('Token gerado com sucesso');
            
            // Get form data
            const formData = cardFormInstance.getCardFormData();
            
            // Get checkout data for shipping info
            const checkoutData = JSON.parse(sessionStorage.getItem('checkout_data'));
            
            // Add card data to order
            const paymentData = {
                ...orderData,
                shipping_method: {
                    name: checkoutData?.shipping?.name || checkoutData?.shipping?.company || 'Frete',
                    company: checkoutData?.shipping?.company || '',
                    delivery_time: checkoutData?.shipping?.delivery_time || ''
                },
                card_token: tokenData.token,
                installments: parseInt(formData.installments),
                payment_method_id: formData.paymentMethodId,
                issuer_id: formData.issuerId || ''
            };
            
            console.log('Enviando pagamento para API...');
            
            // Send payment to API
            const token = sessionStorage.getItem('customer_token');
            const response = await $.ajax({
                url: RODUST_PAYMENT.api_url + '/api/payments/card',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify(paymentData)
            });
            
            hideLoading();
            
            if (response.success) {
                console.log('Pagamento processado com sucesso');
                return response.data;
            } else {
                throw new Error(response.message || 'Erro ao processar pagamento');
            }
            
        } catch (error) {
            hideLoading();
            console.error('Erro no pagamento:', error);
            
            let errorMsg = 'Erro ao processar pagamento. Verifique os dados do cartão.';
            if (error.responseJSON && error.responseJSON.message) {
                errorMsg = error.responseJSON.message;
            } else if (error.message) {
                errorMsg = error.message;
            }
            
            showError(errorMsg);
            return null;
        }
    };
    
    /**
     * Show error message
     */
    function showError(message) {
        $('#mp-card-errors').text(message).show();
    }
    
    /**
     * Hide error message
     */
    function hideError() {
        $('#mp-card-errors').hide();
    }
    
    /**
     * Show loading state
     */
    function showLoading() {
        $('#mp-card-loading').show();
        $('#btn-finalize-payment').prop('disabled', true);
    }
    
    /**
     * Hide loading state
     */
    function hideLoading() {
        $('#mp-card-loading').hide();
        $('#btn-finalize-payment').prop('disabled', false);
    }
    
    /**
     * Initialize when payment method is selected
     */
    $(document).on('click', '.payment-method', function() {
        const method = $(this).find('input[type="radio"]').val();
        
        if (method === 'credit_card' && !mpInstance && typeof MercadoPago !== 'undefined') {
            // Aguardar formulário aparecer
            setTimeout(() => {
                initMercadoPago();
            }, 100);
        }
    });
    
    // Debug
    $(document).ready(function() {
        console.log('Mercado Pago Card JS carregado');
    });
    
})(jQuery);
