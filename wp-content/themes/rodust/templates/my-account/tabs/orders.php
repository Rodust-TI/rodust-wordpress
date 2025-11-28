<?php
/**
 * My Account Tab - Orders
 */
?>

<div id="tab-pedidos" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Meus Pedidos</h2>
        
        <!-- Loading -->
        <div id="orders-loading" class="text-center py-8">
            <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-gray-600 mt-4">Carregando pedidos...</p>
        </div>
        
        <!-- Lista de Pedidos -->
        <div id="orders-list" class="hidden space-y-4"></div>
        
        <!-- Paginação -->
        <div id="orders-pagination" class="hidden mt-6 flex justify-center items-center gap-2"></div>
        
        <!-- Mensagem Vazia -->
        <div id="orders-empty" class="hidden text-center py-12">
            <div class="text-6xl mb-4">📦</div>
            <p class="text-gray-600 text-lg mb-2">Você ainda não possui pedidos</p>
            <p class="text-gray-500 text-sm mb-6">Comece a explorar nossos produtos</p>
            <a href="<?php echo get_post_type_archive_link('rodust_product'); ?>" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                Ver Produtos
            </a>
        </div>
    </div>
</div>
