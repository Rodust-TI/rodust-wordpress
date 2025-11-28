<?php
/**
 * My Account Tab - Personal Data
 */
?>

<div id="tab-dados" class="tab-content">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Dados Pessoais</h2>
        
        <div id="update-messages" class="mb-6 hidden"></div>

        <form id="update-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nome Completo -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                    <input type="text" id="update-name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Tipo de Pessoa -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Pessoa *</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="person_type" id="person-type-f" value="F" checked class="form-radio text-blue-600">
                            <span class="ml-2">Pessoa Física</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="person_type" id="person-type-j" value="J" class="form-radio text-blue-600">
                            <span class="ml-2">Pessoa Jurídica</span>
                        </label>
                    </div>
                </div>

                <!-- CPF/CNPJ -->
                <div id="field-cpf">
                    <label class="block text-sm font-medium text-gray-700 mb-2">CPF *</label>
                    <input type="text" id="update-cpf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="000.000.000-00" maxlength="14">
                    <p class="text-xs text-gray-500 mt-1">Somente números</p>
                </div>

                <div id="field-cnpj" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">CNPJ *</label>
                    <input type="text" id="update-cnpj" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="00.000.000/0000-00" maxlength="18">
                    <p class="text-xs text-gray-500 mt-1">Somente números</p>
                </div>

                <!-- Data de Nascimento (só PF) -->
                <div id="field-birth-date">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                    <input type="date" id="update-birth-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Nome Fantasia (só PJ) -->
                <div id="field-fantasy-name" class="hidden col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Fantasia</label>
                    <input type="text" id="update-fantasy-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Como a empresa é conhecida">
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefone Celular *</label>
                    <input type="tel" id="update-phone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="(00) 00000-0000">
                </div>

                <!-- Telefone Comercial -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefone Comercial</label>
                    <input type="tel" id="update-phone-commercial" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="(00) 0000-0000">
                </div>

                <!-- E-mail Principal -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail *</label>
                    <input type="email" id="update-email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- E-mail para NF-e -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail para NF-e (opcional)</label>
                    <input type="email" id="update-nfe-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deixe vazio para usar o e-mail principal">
                    <p class="text-xs text-gray-500 mt-1">E-mail que receberá as Notas Fiscais Eletrônicas</p>
                </div>

                <!-- Inscrição Estadual e Estado (só PJ) -->
                <div id="field-state-registration" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inscrição Estadual (IE) *</label>
                    <input type="text" id="update-state-registration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="535.371.914.110" maxlength="15">
                    <p class="text-xs text-gray-500 mt-1">12 dígitos (somente números)</p>
                </div>

                <div id="field-state-uf" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado (UF) *</label>
                    <select id="update-state-uf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                    <p class="text-xs text-blue-600 mt-1">💡 Estado onde a empresa está registrada</p>
                </div>

                <!-- Alterar Senha -->
                <div class="col-span-2 border-t pt-6 mt-4">
                    <h3 class="text-lg font-semibold mb-4">Alterar Senha</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                    <input type="password" id="update-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deixe em branco para manter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha</label>
                    <input type="password" id="update-password-confirm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            <div class="mt-8">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    💾 Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
