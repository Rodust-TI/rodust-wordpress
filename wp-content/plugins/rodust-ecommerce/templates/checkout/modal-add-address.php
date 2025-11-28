<!-- Modal: Adicionar Novo Endereço -->
<div id="new-address-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid #e0e0e0;">
            <h3 style="margin: 0; font-size: 20px;"><?php _e('Adicionar Novo Endereço', 'rodust-ecommerce'); ?></h3>
        </div>
        
        <div id="new-address-form" style="padding: 20px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('CEP', 'rodust-ecommerce'); ?> *</label>
                <input type="text" id="modal_postal_code" maxlength="9" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                <button type="button" id="modal-search-postal-code" style="margin-top: 8px; background: transparent; border: none; color: #007bff; cursor: pointer; padding: 0; font-size: 14px;"><?php _e('Buscar CEP', 'rodust-ecommerce'); ?></button>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Logradouro', 'rodust-ecommerce'); ?> *</label>
                    <input type="text" id="modal_street" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Número', 'rodust-ecommerce'); ?> *</label>
                    <input type="text" id="modal_number" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Complemento', 'rodust-ecommerce'); ?></label>
                    <input type="text" id="modal_complement" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Bairro', 'rodust-ecommerce'); ?> *</label>
                    <input type="text" id="modal_neighborhood" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Cidade', 'rodust-ecommerce'); ?> *</label>
                    <input type="text" id="modal_city" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('UF', 'rodust-ecommerce'); ?> *</label>
                    <select id="modal_state" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                        <option value="">Selecione</option>
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MT">MT</option>
                        <option value="MS">MS</option>
                        <option value="MG">MG</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PR">PR</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RS">RS</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="SC">SC</option>
                        <option value="SP">SP</option>
                        <option value="SE">SE</option>
                        <option value="TO">TO</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;"><?php _e('Identificação (opcional)', 'rodust-ecommerce'); ?></label>
                <input type="text" id="modal_label" placeholder="Ex: Casa, Trabalho..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>
        
        <div style="padding: 16px 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 12px;">
            <button type="button" id="btn-save-new-address" style="flex: 1; background: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                <?php _e('Salvar Endereço', 'rodust-ecommerce'); ?>
            </button>
            <button type="button" id="btn-cancel-new-address" style="padding: 12px 24px; background: white; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                <?php _e('Cancelar', 'rodust-ecommerce'); ?>
            </button>
        </div>
    </div>
</div>
