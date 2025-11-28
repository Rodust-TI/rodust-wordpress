<?php
/**
 * Rodust Ecommerce - Configurações Administrativas
 * 
 * Painel de configuração para controlar elementos visuais e integrações
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar submenus dentro do menu do plugin Rodust Ecommerce
 */
add_action('admin_menu', 'rodust_header_elements_admin_menu', 20);

function rodust_header_elements_admin_menu() {
    // Submenu: Elementos do Header (dentro do menu do plugin)
    add_submenu_page(
        'rodust-ecommerce',              // Parent slug (do plugin)
        'Elementos do Header',           // Título da página
        'Elementos do Header',           // Título do menu
        'manage_options',                // Capacidade necessária
        'rodust-header-elements',        // Slug único
        'rodust_ecommerce_header_elements_page'
    );
}

/**
 * Página: Elementos do Header
 */
function rodust_ecommerce_header_elements_page() {
    // Salvar configurações
    if (isset($_POST['rodust_header_elements_submit'])) {
        check_admin_referer('rodust_header_elements_save');
        
        update_option('rodust_show_cart_icon', isset($_POST['show_cart_icon']) ? '1' : '0');
        update_option('rodust_show_user_icon', isset($_POST['show_user_icon']) ? '1' : '0');
        update_option('rodust_show_search_icon', isset($_POST['show_search_icon']) ? '1' : '0');
        update_option('rodust_show_wishlist_icon', isset($_POST['show_wishlist_icon']) ? '1' : '0');
        
        echo '<div class="notice notice-success"><p>Configurações salvas com sucesso!</p></div>';
    }

    // Valores atuais
    $show_cart = get_option('rodust_show_cart_icon', '1');
    $show_user = get_option('rodust_show_user_icon', '1');
    $show_search = get_option('rodust_show_search_icon', '0');
    $show_wishlist = get_option('rodust_show_wishlist_icon', '0');
    ?>
    <div class="wrap">
        <h1>Elementos do Header</h1>
        <p>Ative ou desative elementos visuais que aparecem no cabeçalho do site.</p>

        <form method="post" action="">
            <?php wp_nonce_field('rodust_header_elements_save'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="show_cart_icon">🛒 Ícone do Carrinho</label>
                    </th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   id="show_cart_icon" 
                                   name="show_cart_icon" 
                                   value="1" 
                                   <?php checked($show_cart, '1'); ?>>
                            <span class="description">Mostrar ícone do carrinho de compras no header</span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="show_user_icon">👤 Ícone do Usuário</label>
                    </th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   id="show_user_icon" 
                                   name="show_user_icon" 
                                   value="1" 
                                   <?php checked($show_user, '1'); ?>>
                            <span class="description">Mostrar ícone de login/perfil do usuário no header</span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="show_search_icon">🔍 Ícone de Busca</label>
                    </th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   id="show_search_icon" 
                                   name="show_search_icon" 
                                   value="1" 
                                   <?php checked($show_search, '1'); ?>>
                            <span class="description">Mostrar ícone de busca no header (em desenvolvimento)</span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="show_wishlist_icon">❤️ Ícone da Lista de Desejos</label>
                    </th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   id="show_wishlist_icon" 
                                   name="show_wishlist_icon" 
                                   value="1" 
                                   <?php checked($show_wishlist, '1'); ?>>
                            <span class="description">Mostrar ícone de lista de desejos no header (em desenvolvimento)</span>
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button('Salvar Configurações', 'primary', 'rodust_header_elements_submit'); ?>
        </form>

        <div class="card" style="max-width: 600px; margin-top: 30px;">
            <h3>💡 Como funciona?</h3>
            <p>Os ícones ativados aqui aparecerão automaticamente no cabeçalho do site, ao lado do logotipo e menu de navegação.</p>
            <p><strong>Nota:</strong> Alguns ícones ainda estão em desenvolvimento e podem não ter funcionalidade completa.</p>
        </div>
    </div>
    <?php
}
