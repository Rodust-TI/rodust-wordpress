<?php
/**
 * Payment - Credit Card Form
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

<div id="credit-card-form" class="payment-form hidden">
    <form id="mp-card-form" style="max-width: 500px;">
        
        <!-- Card Number -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="cardNumber" style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php _e('Número do Cartão', 'rodust-ecommerce'); ?>
            </label>
            <input 
                type="text" 
                id="cardNumber" 
                name="cardNumber" 
                placeholder="0000 0000 0000 0000"
                autocomplete="cc-number"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                required
            />
            <div id="issuerInput" style="margin-top: 10px;"></div>
        </div>

        <!-- Cardholder Name -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="cardholderName" style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php _e('Nome do Titular', 'rodust-ecommerce'); ?>
            </label>
            <input 
                type="text" 
                id="cardholderName" 
                name="cardholderName"
                placeholder="Como está no cartão"
                autocomplete="cc-name"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                required
            />
        </div>

        <!-- Expiration & CVV -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="cardExpirationDate" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    <?php _e('Validade', 'rodust-ecommerce'); ?>
                </label>
                <input 
                    type="text" 
                    id="cardExpirationDate" 
                    name="cardExpirationDate"
                    placeholder="MM/AA"
                    autocomplete="cc-exp"
                    maxlength="5"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                    required
                />
            </div>

            <div class="form-group">
                <label for="securityCode" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    <?php _e('CVV', 'rodust-ecommerce'); ?>
                </label>
                <input 
                    type="text" 
                    id="securityCode" 
                    name="securityCode"
                    placeholder="123"
                    autocomplete="cc-csc"
                    maxlength="4"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                    required
                />
            </div>
        </div>

        <!-- Document -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="cardholderDocument" style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php _e('CPF do Titular', 'rodust-ecommerce'); ?>
            </label>
            <input 
                type="text" 
                id="cardholderDocument" 
                name="cardholderDocument"
                placeholder="000.000.000-00"
                maxlength="14"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                required
            />
        </div>

        <!-- Installments -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="installments" style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php _e('Parcelas', 'rodust-ecommerce'); ?>
            </label>
            <select 
                id="installments" 
                name="installments"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                required
            >
                <option value=""><?php _e('Selecione...', 'rodust-ecommerce'); ?></option>
            </select>
        </div>

        <!-- Hidden fields for Mercado Pago -->
        <input type="hidden" id="paymentMethodId" name="paymentMethodId" />
        <input type="hidden" id="issuerId" name="issuerId" />
        <input type="hidden" id="cardToken" name="cardToken" />
        
        <!-- Form errors -->
        <div id="mp-card-errors" class="error-message" style="display: none; padding: 12px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33; margin-bottom: 20px;"></div>
        
        <!-- Loading indicator -->
        <div id="mp-card-loading" style="display: none; text-align: center; padding: 20px;">
            <p><?php _e('Processando pagamento...', 'rodust-ecommerce'); ?></p>
        </div>
        
    </form>

    <div class="security-badges" style="display: flex; align-items: center; gap: 10px; margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
        <svg width="20" height="20" fill="#28a745" viewBox="0 0 16 16">
            <path d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H8V0z"/>
            <path d="M8 0v11h4.219C14.292 11 16 9.366 16 7.318c0-1.763-1.266-3.223-2.942-3.593-.143-.863-.698-1.723-1.464-2.383A5.53 5.53 0 0 0 8 0z"/>
        </svg>
        <span style="font-size: 13px; color: #666;">
            <?php _e('Pagamento 100% seguro processado pelo Mercado Pago', 'rodust-ecommerce'); ?>
        </span>
    </div>
</div>
