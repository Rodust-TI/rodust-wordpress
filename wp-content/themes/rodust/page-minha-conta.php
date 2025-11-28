<?php
/**
 * Template Name: Minha Conta (Refatorado)
 * 
 * Estrutura modular para área do cliente
 * Cada aba é um arquivo separado para melhor manutenibilidade
 */

get_header();
?>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 w-full max-w-md space-y-2" style="pointer-events: none;">
    <!-- Toasts serão inseridos aqui via JS -->
</div>

<main class="container mx-auto px-4 py-12 md:py-16">
    
    <?php 
    // Header da área do cliente
    get_template_part('templates/my-account/partials/header'); 
    ?>

    <!-- Mensagem de não autenticado -->
    <div id="not-authenticated" class="hidden bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded mb-8">
        <p class="text-yellow-700 mb-4">Você precisa estar logado para acessar esta página.</p>
        <a href="<?php echo home_url('/login'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
            Fazer Login
        </a>
    </div>

    <!-- Conteúdo da área do cliente -->
    <div id="customer-area" class="hidden">
        
        <?php 
        // Navegação em Abas
        get_template_part('templates/my-account/partials/navigation'); 
        ?>

        <!-- Abas de Conteúdo -->
        <?php 
        // Aba: Dados Pessoais
        get_template_part('templates/my-account/tabs/personal-data');
        
        // Aba: Endereços
        get_template_part('templates/my-account/tabs/addresses');
        
        // Aba: Pedidos
        get_template_part('templates/my-account/tabs/orders');
        
        // Aba: Lista de Desejos
        get_template_part('templates/my-account/tabs/wishlist');
        ?>

    </div>
</main>

<!-- Modais -->
<?php 
get_template_part('templates/my-account/modals/address-form');
get_template_part('templates/my-account/modals/order-details');
?>

<?php 
// Enfileirar scripts modulares com versão dinâmica para evitar cache
$js_dir = get_template_directory() . '/assets/js/my-account/';
$js_uri = get_template_directory_uri() . '/assets/js/my-account/';

// Adicionar API URL inline ANTES do primeiro módulo
$api_url = home_url('/wp-json/rodust-proxy/v1');

$modules = ['main', 'personal-data', 'addresses', 'orders', 'wishlist'];
foreach ($modules as $index => $module) {
    $file_path = $js_dir . $module . '.js';
    $version = file_exists($file_path) ? filemtime($file_path) : time();
    $deps = ($index === 0) ? array('jquery') : array('jquery', 'my-account-main');
    wp_enqueue_script('my-account-' . $module, $js_uri . $module . '.js', $deps, $version, true);
    
    // Adicionar API URL e HOME URL inline apenas no primeiro script (main)
    if ($index === 0) {
        $inline_script = sprintf(
            'window.RODUST_API_URL = %s; window.RODUST_HOME_URL = %s; console.log("[Minha Conta] API URL:", window.RODUST_API_URL);',
            wp_json_encode($api_url),
            wp_json_encode(home_url())
        );
        wp_add_inline_script('my-account-main', $inline_script, 'before');
    }
}

get_footer();
?>
