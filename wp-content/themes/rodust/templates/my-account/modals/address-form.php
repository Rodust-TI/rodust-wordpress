<?php
/**
 * Modal - Address Form
 */
?>

<div id="address-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold" id="modal-title">Novo Endereço</h3>
        </div>
        
        <form id="address-form" class="p-6">
            <input type="hidden" id="address-id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Label -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Identificação (opcional)</label>
                    <input type="text" id="address-label" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Casa, Trabalho, Escritório">
                </div>

                <!-- Nome do Destinatário -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Destinatário</label>
                    <input type="text" id="address-recipient" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Quem vai receber">
                </div>

                <!-- CEP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CEP *</label>
                    <input type="text" id="address-zipcode" maxlength="9" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="00000-000" required>
                    <p class="text-xs text-blue-600 mt-1 cursor-pointer hover:underline" id="search-zipcode">Buscar endereço pelo CEP</p>
                </div>

                <div></div>

                <!-- Logradouro -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logradouro *</label>
                    <input type="text" id="address-street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Número -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número *</label>
                    <input type="text" id="address-number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Complemento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                    <input type="text" id="address-complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Bairro -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bairro *</label>
                    <input type="text" id="address-neighborhood" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Cidade -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cidade *</label>
                    <input type="text" id="address-city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select id="address-state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Selecione...</option>
                        <option value="AC">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amapá</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Ceará</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Espírito Santo</option>
                        <option value="GO">Goiás</option>
                        <option value="MA">Maranhão</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Pará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PR">Paraná</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piauí</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rondônia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">São Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                    </select>
                </div>

                <!-- Usar como Entrega / Cobrança (apenas na edição) -->
                <div class="col-span-2" id="type-toggles" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Usar este endereço como:</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" id="is-shipping" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">
                                <span class="font-medium">📦 Endereço de Entrega</span>
                                <span class="block text-xs text-gray-500 mt-1">Onde os produtos serão entregues</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" id="is-billing" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">
                                <span class="font-medium">💳 Endereço de Cobrança</span>
                                <span class="block text-xs text-gray-500 mt-1">Para emissão de nota fiscal</span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            <div class="flex gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    Salvar Endereço
                </button>
                <button type="button" id="btn-cancel-address" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
