<?php
/**
 * My Account - Tab Navigation
 */
?>

<div class="border-b border-gray-200 mb-8">
    <nav class="flex space-x-8">
        <button class="tab-btn active py-4 px-2 border-b-2 border-blue-600 text-blue-600 font-medium" data-tab="dados">
            Dados Pessoais
        </button>
        <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="enderecos">
            Endereços
        </button>
        <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="pedidos">
            Pedidos
        </button>
        <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-gray-300 text-gray-600 hover:text-gray-900 font-medium" data-tab="wishlist">
            Lista de Desejos
        </button>
        <button class="tab-btn py-4 px-2 border-b-2 border-transparent hover:border-red-300 text-gray-600 hover:text-red-600 font-medium" onclick="MyAccount.logout()">
            Sair
        </button>
    </nav>
</div>
