<?php
/**
 * My Account Tab - Addresses
 */
?>

<div id="tab-enderecos" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Meus Endereços</h2>
            <button id="btn-new-address" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                + Novo Endereço
            </button>
        </div>

        <!-- Lista de Endereços -->
        <div id="addresses-list" class="space-y-4">
            <p class="text-gray-500 text-center py-8">Carregando endereços...</p>
        </div>
    </div>
</div>
