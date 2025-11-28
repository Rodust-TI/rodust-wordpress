/**
 * Mercado Pago - Card Payment Integration
 * 
 * @package RodustEcommerce
 */

(function($) {
    'use strict';
    
    let mpInstance = null;
    let cardToken = null;
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
        const cardForm = mpInstance.cardForm({
            amount: getOrderTotal(),
            autoMount: false,
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
                    return;
                },
                onCardTokenReceived: (error, token) => {
                    if (error) {
                        console.error('Erro ao gerar token:', error);
                        showError('Dados do cartão inválidos');
                    } else {
                        cardToken = token;
                        console.log('Token gerado:', token);
                    }
                }
            },
        });
        
        // Mount the form
        cardForm.mount();
        
        // Store for later use
        window.RodustMPCardForm = cardForm;
    }
    
    /**
     * Apply Brazilian masks to inputs
     */
    function applyBrazilianMasks() {
        // CPF mask
        $('#cardholderDocument').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            $(this).val(value);
        });
        
        // Expiration date mask
        $('#cardExpirationDate').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            $(this).val(value);
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
    
    /**
     * Create card payment (called from main payment.js)
     */
    window.processCardPayment = async function(orderData) {
        try {
            // Validate form
            const form = document.getElementById('mp-card-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }
            
            showLoading();
            
            // Get form data
            const cardForm = window.RodustMPCardForm;
            const formData = cardForm.getCardFormData();
            
            console.log('Dados do formulário:', formData);
            
            // Create token
            await new Promise((resolve, reject) => {
                cardForm.createCardToken({
                    cardholderName: formData.cardholderName,
                    identificationType: formData.identificationType,
                    identificationNumber: formData.identificationNumber.replace(/\D/g, '')
                }).then(token => {
                    cardToken = token.id;
                    resolve();
                }).catch(error => {
                    console.error('Erro ao criar token:', error);
                    reject(error);
                });
            });
            
            if (!cardToken) {
                throw new Error('Não foi possível gerar o token do cartão');
            }
            
            // Add card data to order
            const paymentData = {
                ...orderData,
                card_token: cardToken,
                installments: parseInt(formData.installments),
                payment_method_id: formData.paymentMethodId,
                issuer_id: formData.issuerId
            };
            
            console.log('Enviando pagamento:', paymentData);
            
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
            return false;
        }
    };
    
    /**
     * Show error message
     */
    function showError(message) {
        $('#mp-card-errors').text(message).show();
        setTimeout(() => {
            $('#mp-card-errors').fadeOut();
        }, 5000);
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
    $(document).on('click', '.payment-method[data-method="credit_card"]', function() {
        if (!mpInstance && typeof MercadoPago !== 'undefined') {
            initMercadoPago();
        }
    });
    
    // Also initialize if card form is already visible on page load
    $(document).ready(function() {
        if ($('#credit-card-form').is(':visible') && typeof MercadoPago !== 'undefined') {
            initMercadoPago();
        }
    });
    
})(jQuery);
