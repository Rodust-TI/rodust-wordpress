<?php
/**
 * Modal - Order Details
 */
?>

<div id="order-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold" id="modal-order-title">Detalhes do Pedido</h3>
            <button id="btn-close-order-details" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
        </div>
        
        <div id="order-details-content" class="p-6">
            <!-- Conteúdo será inserido via JS -->
        </div>
    </div>
</div>
