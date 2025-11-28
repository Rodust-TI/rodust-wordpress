<?php
/**
 * Checkout Component: Address Section
 * 
 * Seção de endereço de entrega com seletor e formulário
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;
?>

<!-- Endereço de Entrega -->
<div class="checkout-section">
    <h2><?php _e('Endereço de Entrega', 'rodust-ecommerce'); ?></h2>
    
    <!-- Box Endereço Selecionado (visível quando há endereço padrão) -->
    <div id="selected-address-box" style="display: none;">
        <div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="margin: 0 0 4px 0; font-weight: 600;" id="selected-address-label"></p>
                    <p style="margin: 0 0 2px 0; font-size: 14px;" id="selected-address-line1"></p>
                    <p style="margin: 0; font-size: 14px; color: #666;" id="selected-address-line2"></p>
                </div>
                <button type="button" id="btn-change-address" style="background: #fff; border: 1px solid #ddd; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                    <?php _e('Trocar endereço', 'rodust-ecommerce'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Lista de Endereços (oculta inicialmente) -->
    <div id="addresses-list-section" style="display: none; margin-bottom: 20px;">
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; background: #fff;">
            <h3 style="margin-top: 0; font-size: 16px;"><?php _e('Selecione um endereço', 'rodust-ecommerce'); ?></h3>
            <div id="addresses-list"></div>
            <button type="button" id="btn-add-new-address" style="margin-top: 12px; background: #007bff; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; width: 100%;">
                + <?php _e('Adicionar outro endereço', 'rodust-ecommerce'); ?>
            </button>
        </div>
    </div>
    
    <!-- Formulário de Novo Endereço -->
    <div id="address-form-section">
        <div class="form-row">
            <div class="form-group form-group-small">
                <label for="address_zipcode"><?php _e('CEP', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="address_zipcode" name="address_zipcode" maxlength="9" required>
                <button type="button" id="btn-search-cep" class="btn-link"><?php _e('Buscar CEP', 'rodust-ecommerce'); ?></button>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group form-group-large">
                <label for="address_address"><?php _e('Endereço', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="address_address" name="address_address" required>
            </div>
            
            <div class="form-group form-group-small">
                <label for="address_number"><?php _e('Número', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="address_number" name="address_number" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="address_complement"><?php _e('Complemento', 'rodust-ecommerce'); ?></label>
                <input type="text" id="address_complement" name="address_complement">
            </div>
            
            <div class="form-group">
                <label for="address_neighborhood"><?php _e('Bairro', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="address_neighborhood" name="address_neighborhood" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="address_city"><?php _e('Cidade', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="address_city" name="address_city" required>
            </div>
            
            <div class="form-group form-group-small">
                <label for="address_state"><?php _e('Estado', 'rodust-ecommerce'); ?> *</label>
                <select id="address_state" name="address_state" required>
                    <option value="">Selecione</option>
                    <?php
                    $states = [
                        'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                        'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                        'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                        'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                        'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                        'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                        'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                    ];
                    foreach ($states as $uf => $name) {
                        echo "<option value=\"{$uf}\">{$uf}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <!-- Salvar Endereço -->
        <div class="form-row">
            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="save_address" name="save_address" value="1">
                    <span><?php _e('Salvar este endereço para próximas compras', 'rodust-ecommerce'); ?></span>
                </label>
            </div>
        </div>
    </div><!-- Fim #address-form-section -->
</div>
