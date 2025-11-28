/**
 * Rodust Ecommerce - Main JavaScript
 * Handles cart, checkout, and product interactions
 *
 * @package RodustEcommerce
 */

(function($) {
    'use strict';

    const RodustEcommerce = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.updateCartCount();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Add to cart
            $(document).on('click', '.btn-add-to-cart', this.addToCart.bind(this));
            
            // Update cart quantity
            $(document).on('change', '.cart-table .qty', this.updateCartQuantity.bind(this));
            $(document).on('click', '.qty-plus', this.incrementQuantity.bind(this));
            $(document).on('click', '.qty-minus', this.decrementQuantity.bind(this));
            
            // Remove from cart
            $(document).on('click', '.remove-item', this.removeFromCart.bind(this));
            
            // Clear cart
            $(document).on('click', '.clear-cart', this.clearCart.bind(this));
            
            // Calculate shipping
            $(document).on('click', '#calculate-shipping', this.calculateShipping.bind(this));
            
            // Postal code mask
            $(document).on('input', '#shipping-postal-code', this.formatPostalCode);
            
            // Checkout form
            $(document).on('submit', '#checkout-form', this.processCheckout.bind(this));
            
            // Payment method selection
            $(document).on('change', 'input[name="payment_method"]', this.togglePaymentDetails.bind(this));
        },

        /**
         * Add product to cart
         */
        addToCart: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const productId = $btn.data('product-id');
            
            // Get quantity from qty-input or form or default to 1
            let quantity = 1;
            
            // Tenta pegar do input de quantidade no card (archive)
            const $qtyInput = $btn.closest('article, .product-card').find('.qty-input[data-product-id="' + productId + '"]');
            if ($qtyInput.length) {
                quantity = parseInt($qtyInput.val()) || 1;
            } else {
                // Tenta pegar do input único da página de produto (single)
                const $singleQtyInput = $('#qty-input-single');
                if ($singleQtyInput.length) {
                    quantity = parseInt($singleQtyInput.val()) || 1;
                } else {
                    // Tenta pegar do formulário
                    const $form = $btn.closest('form.cart-form');
                    if ($form.length) {
                        quantity = parseInt($form.find('input[name="quantity"]').val()) || 1;
                    }
                }
            }
            
            const productData = {
                name: $btn.data('name'),
                price: parseFloat($btn.data('price')),
                image: $btn.data('image'),
                sku: $btn.data('sku'),
                stock: parseInt($btn.data('stock'))
            };

            // Show loading state
            const originalText = $btn.html();
            $btn.html('<span class="spinner"></span> ' + rodustEcommerce.i18n.loading).prop('disabled', true);

            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_add_to_cart',
                    nonce: rodustEcommerce.nonce,
                    product_id: productId,
                    quantity: quantity,
                    product_data: productData
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice(rodustEcommerce.i18n.addedToCart, 'success');
                        this.updateCartCount();
                        
                        // Resetar quantidade para 1 após adicionar
                        if ($qtyInput.length) {
                            $qtyInput.val(1);
                        } else if ($('#qty-input-single').length) {
                            $('#qty-input-single').val(1);
                        }
                        
                        // Mini cart update if exists
                        if (typeof response.data.cart_html !== 'undefined') {
                            $('.mini-cart-content').html(response.data.cart_html);
                        }
                    } else {
                        this.showNotice(response.data.message || rodustEcommerce.i18n.error, 'error');
                    }
                },
                error: () => {
                    this.showNotice(rodustEcommerce.i18n.error, 'error');
                },
                complete: () => {
                    $btn.html(originalText).prop('disabled', false);
                }
            });
        },

        /**
         * Update cart item quantity
         */
        updateCartQuantity: function(e) {
            const $input = $(e.currentTarget);
            const productId = $input.data('product-id');
            const quantity = parseInt($input.val());

            if (quantity < 1) {
                return;
            }

            this.updateCartItem(productId, quantity);
        },

        /**
         * Increment quantity
         */
        incrementQuantity: function(e) {
            e.preventDefault();
            const productId = $(e.currentTarget).data('product-id');
            const $input = $(`.qty[data-product-id="${productId}"]`);
            const max = parseInt($input.attr('max'));
            let quantity = parseInt($input.val()) + 1;
            
            if (max && quantity > max) {
                this.showNotice('Quantidade máxima atingida', 'warning');
                return;
            }
            
            $input.val(quantity);
            this.updateCartItem(productId, quantity);
        },

        /**
         * Decrement quantity
         */
        decrementQuantity: function(e) {
            e.preventDefault();
            const productId = $(e.currentTarget).data('product-id');
            const $input = $(`.qty[data-product-id="${productId}"]`);
            let quantity = parseInt($input.val()) - 1;
            
            if (quantity < 1) {
                quantity = 1;
            }
            
            $input.val(quantity);
            this.updateCartItem(productId, quantity);
        },

        /**
         * Update cart item via AJAX
         */
        updateCartItem: function(productId, quantity) {
            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_update_cart',
                    nonce: rodustEcommerce.nonce,
                    product_id: productId,
                    quantity: quantity
                },
                success: (response) => {
                    if (response.success) {
                        // Atualizar subtotal do item individual
                        this.updateItemSubtotal(productId, quantity);
                        // Atualizar totais gerais
                        this.updateCartTotals(response.data);
                    } else {
                        this.showNotice(response.data.message || rodustEcommerce.i18n.error, 'error');
                    }
                }
            });
        },

        /**
         * Update individual item subtotal
         */
        updateItemSubtotal: function(productId, quantity) {
            const $row = $(`.cart-item[data-product-id="${productId}"]`);
            const $priceElement = $row.find('.text-gray-700.text-sm.font-medium.w-24');
            const priceText = $priceElement.text().trim();
            
            // Remove "R$ " e converte para número
            const price = parseFloat(priceText.replace('R$ ', '').replace(/\./g, '').replace(',', '.'));
            const subtotal = price * quantity;
            
            // Formatar o subtotal
            const formattedSubtotal = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            
            // Atualizar o subtotal do item
            $(`.item-subtotal[data-product-id="${productId}"]`).text(formattedSubtotal);
        },

        /**
         * Remove item from cart
         */
        removeFromCart: function(e) {
            e.preventDefault();
            
            if (!confirm('Remover este produto do carrinho?')) {
                return;
            }
            
            const productId = $(e.currentTarget).data('product-id');
            const $row = $(`.cart-item[data-product-id="${productId}"]`);

            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_remove_from_cart',
                    nonce: rodustEcommerce.nonce,
                    product_id: productId
                },
                success: (response) => {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if cart is empty
                            if ($('.cart-item').length === 0) {
                                location.reload();
                            }
                        });
                        this.updateCartTotals(response.data);
                        this.showNotice('Produto removido do carrinho', 'success');
                    }
                }
            });
        },

        /**
         * Clear entire cart
         */
        clearCart: function(e) {
            e.preventDefault();
            
            if (!confirm('Deseja limpar todo o carrinho?')) {
                return;
            }

            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_clear_cart',
                    nonce: rodustEcommerce.nonce
                },
                success: (response) => {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        },

        /**
         * Calculate shipping
         */
        calculateShipping: function(e) {
            e.preventDefault();
            
            const postalCode = $('#shipping-postal-code').val().replace(/\D/g, '');
            
            if (postalCode.length !== 8) {
                this.showNotice('Digite um CEP válido', 'warning');
                return;
            }

            const $btn = $(e.currentTarget);
            const originalText = $btn.text();
            
            $btn.text('Calculando...').prop('disabled', true);
            $('#shipping-options').html('<div class="loading">Consultando transportadoras...</div>');

            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_calculate_shipping',
                    nonce: rodustEcommerce.nonce,
                    postal_code: postalCode
                },
                success: (response) => {
                    if (response.success) {
                        this.displayShippingOptions(response.data.options);
                    } else {
                        $('#shipping-options').html('<div class="error">' + response.data.message + '</div>');
                    }
                },
                error: () => {
                    $('#shipping-options').html('<div class="error">Erro ao calcular frete</div>');
                },
                complete: () => {
                    $btn.text(originalText).prop('disabled', false);
                }
            });
        },

        /**
         * Display shipping options
         */
        displayShippingOptions: function(options) {
            if (!options || options.length === 0) {
                $('#shipping-options').html('<div class="warning">Nenhuma opção de frete disponível para este CEP.</div>');
                return;
            }

            let html = '<div class="shipping-options-list">';
            
            options.forEach((option, index) => {
                html += `
                    <label class="shipping-option">
                        <input type="radio" name="shipping_option" value="${option.id}" data-price="${option.price}" ${index === 0 ? 'checked' : ''}>
                        <div class="option-details">
                            <strong>${option.name}</strong>
                            <span class="company">${option.company}</span>
                            <span class="delivery-time">${option.formatted_time}</span>
                        </div>
                        <div class="option-price">
                            ${option.formatted_price}
                        </div>
                    </label>
                `;
            });
            
            html += '</div>';
            
            $('#shipping-options').html(html);
            
            // Update total with first option selected
            this.updateShippingCost(options[0].price);
            
            // Handle shipping option change
            $(document).on('change', 'input[name="shipping_option"]', (e) => {
                const price = parseFloat($(e.currentTarget).data('price'));
                this.updateShippingCost(price);
            });
        },

        /**
         * Update shipping cost in totals
         */
        updateShippingCost: function(cost) {
            const subtotal = parseFloat($('.cart-subtotal .amount').text().replace(/[^\d,]/g, '').replace(',', '.'));
            const total = subtotal + cost;
            
            $('.shipping-row .amount').text('R$ ' + cost.toFixed(2).replace('.', ','));
            $('.order-total .total-amount').text('R$ ' + total.toFixed(2).replace('.', ','));
        },

        /**
         * Update cart totals
         */
        updateCartTotals: function(data) {
            if (data.subtotal !== undefined) {
                $('.cart-subtotal .amount').text('R$ ' + data.subtotal.toFixed(2).replace('.', ','));
            }
            if (data.total !== undefined) {
                $('.order-total .total-amount').text('R$ ' + data.total.toFixed(2).replace('.', ','));
            }
            this.updateCartCount();
        },

        /**
         * Update cart count badge
         */
        updateCartCount: function() {
            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_get_cart_count',
                    nonce: rodustEcommerce.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const count = response.data.count;
                        
                        // Atualizar contadores antigos
                        $('.cart-count').text(count);
                        
                        // Atualizar badges do header
                        $('#cart-count-badge, #cart-count-badge-mobile').text(count);
                        
                        if (count > 0) {
                            $('.cart-count').show();
                            $('#cart-count-badge, #cart-count-badge-mobile').removeClass('hidden').show();
                        } else {
                            $('.cart-count').hide();
                            $('#cart-count-badge, #cart-count-badge-mobile').addClass('hidden').hide();
                        }
                    }
                }
            });
        },

        /**
         * Process checkout
         */
        processCheckout: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $submitBtn = $form.find('button[type="submit"]');
            const formData = $form.serializeArray();
            
            // Validate required fields
            let isValid = true;
            $form.find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    isValid = false;
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                this.showNotice('Preencha todos os campos obrigatórios', 'error');
                return;
            }

            $submitBtn.text('Processando...').prop('disabled', true);

            $.ajax({
                url: rodustEcommerce.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rodust_process_checkout',
                    nonce: rodustEcommerce.nonce,
                    form_data: formData
                },
                success: (response) => {
                    if (response.success) {
                        // Redirect to payment gateway or success page
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        } else {
                            this.showNotice('Pedido criado com sucesso!', 'success');
                            setTimeout(() => {
                                window.location.href = '/pedido-confirmado';
                            }, 1500);
                        }
                    } else {
                        this.showNotice(response.data.message || 'Erro ao processar pedido', 'error');
                        $submitBtn.text('Finalizar Compra').prop('disabled', false);
                    }
                },
                error: () => {
                    this.showNotice('Erro ao processar pedido. Tente novamente.', 'error');
                    $submitBtn.text('Finalizar Compra').prop('disabled', false);
                }
            });
        },

        /**
         * Toggle payment method details
         */
        togglePaymentDetails: function(e) {
            const method = $(e.currentTarget).val();
            
            $('.payment-details').hide();
            $(`.payment-details[data-method="${method}"]`).show();
        },

        /**
         * Format postal code (00000-000)
         */
        formatPostalCode: function(e) {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            
            $(this).val(value);
        },

        /**
         * Show notification
         */
        showNotice: function(message, type = 'info') {
            const $notice = $(`
                <div class="rodust-notice rodust-notice-${type}">
                    <span class="notice-message">${message}</span>
                    <button class="notice-close">×</button>
                </div>
            `);
            
            $('body').append($notice);
            
            setTimeout(() => {
                $notice.addClass('show');
            }, 100);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                $notice.removeClass('show');
                setTimeout(() => $notice.remove(), 300);
            }, 5000);
            
            // Close button
            $notice.find('.notice-close').on('click', function() {
                $notice.removeClass('show');
                setTimeout(() => $notice.remove(), 300);
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        RodustEcommerce.init();
    });

})(jQuery);
