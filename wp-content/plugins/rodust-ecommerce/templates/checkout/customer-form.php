<?php
/**
 * Checkout Component: Customer Form
 * 
 * Formulário de dados pessoais do cliente
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

<!-- Dados do Cliente -->
<div class="checkout-section">
    <h2><?php _e('Dados Pessoais', 'rodust-ecommerce'); ?></h2>
    
    <div class="form-row">
        <div class="form-group">
            <label for="customer_name"><?php _e('Nome Completo', 'rodust-ecommerce'); ?> *</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="customer_email"><?php _e('E-mail', 'rodust-ecommerce'); ?> *</label>
            <input type="email" id="customer_email" name="customer_email" required>
        </div>
        
        <div class="form-group">
            <label for="customer_phone"><?php _e('Telefone', 'rodust-ecommerce'); ?> *</label>
            <input type="tel" id="customer_phone" name="customer_phone" required>
        </div>
    </div>
    
    <!-- Seleção de Documento -->
    <div class="form-row">
        <div class="form-group">
            <label><?php _e('Tipo de Documento', 'rodust-ecommerce'); ?> *</label>
            <div class="document-type-selector">
                <label class="document-option">
                    <input type="radio" name="document_type" value="cpf" checked>
                    <span>CPF (Pessoa Física)</span>
                </label>
                <label class="document-option" id="cnpj-option">
                    <input type="radio" name="document_type" value="cnpj">
                    <span>CNPJ (Pessoa Jurídica)</span>
                </label>
            </div>
            <small id="cnpj-warning" class="text-muted hidden">
                ⚠️ <?php _e('Para emitir NF-e como PJ, cadastre CNPJ, Inscrição Estadual e Estado no seu perfil', 'rodust-ecommerce'); ?>
            </small>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="customer_document" id="document-label"><?php _e('CPF', 'rodust-ecommerce'); ?> *</label>
            <input type="text" id="customer_document" name="customer_document" required readonly>
            <small class="text-muted"><?php _e('Documento cadastrado no seu perfil', 'rodust-ecommerce'); ?></small>
        </div>
    </div>
</div>
