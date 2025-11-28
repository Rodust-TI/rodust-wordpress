/**
 * Checkout Initialization
 * Main entry point for checkout functionality
 */

jQuery(document).ready(function($) {
    console.log('Checkout carregado');
    console.log('Dados do carrinho:', RODUST_CHECKOUT_DATA.cart_items);
    
    // Inicializar checkout
    loadCustomerData();
    
    // Atualizar total inicial
    updateOrderTotal();
});
