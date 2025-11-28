<?php
/**
 * Tema Rodust - Functions
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configurações básicas do tema
 */
function rodust_setup() {
    // Suporte a título dinâmico
    add_theme_support('title-tag');
    
    // Suporte a imagens destacadas
    add_theme_support('post-thumbnails');
    
    // Suporte a logo customizável
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    ));
    
    // Suporte a menus
    add_theme_support('menus');
    
    // Registrar menus
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'rodust'),
        'footer' => __('Menu Rodapé', 'rodust'),
    ));
    
    // Suporte a HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form', 
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'rodust_setup');

/**
 * Incluir arquivos do tema
 */
require_once get_template_directory() . '/inc/admin-settings.php';
require_once get_template_directory() . '/includes/rest-api-products.php';
require_once get_template_directory() . '/includes/api-proxy.php';

/**
 * Obter URL da API Laravel (configurável)
 */
function rodust_get_api_url() {
    // Verificar se está definido em wp-config.php (prioridade)
    if (defined('RODUST_API_URL')) {
        return RODUST_API_URL;
    }
    
    // Verificar se está em uma opção do WordPress
    $api_url = get_option('rodust_api_url');
    if ($api_url) {
        return rtrim($api_url, '/');
    }
    
    // Detectar automaticamente baseado no ambiente
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Se for localhost, usar Laravel Sail
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        // Desenvolvimento: Laravel via rede Docker
        return 'http://laravel.test';
    }
    
    // Produção: usar HTTPS
    return 'https://api.' . $host;
}

/**
 * Adicionar URL da API no JavaScript global
 * Movido para inline script antes dos demais scripts
 */

/**
 * Enqueue scripts e styles
 */
function rodust_scripts() {
    // Google Fonts - Outfit (geométrica moderna)
    wp_enqueue_style('rodust-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap', array(), null);
    
    // CSS compilado do Tailwind
    $version = filemtime(get_template_directory() . '/assets/css/style.css');
    wp_enqueue_style('rodust-style', get_template_directory_uri() . '/assets/css/style.css', array('rodust-fonts'), $version);
    
    // jQuery (carregar primeiro)
    wp_enqueue_script('jquery');
    
    // JavaScript personalizado com versão dinâmica
    $js_version = filemtime(get_template_directory() . '/assets/js/script.js');
    wp_enqueue_script('rodust-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), $js_version, true);
    
    // Adicionar API URL inline ANTES do script principal
    // Usar proxy WordPress para evitar Mixed Content (HTTPS → HTTP)
    $api_url = home_url('/wp-json/rodust-proxy/v1');
    $inline_script = sprintf(
        'window.RODUST_API_URL = %s; console.log("API URL configurada (via proxy):", window.RODUST_API_URL);',
        wp_json_encode($api_url)
    );
    wp_add_inline_script('rodust-script', $inline_script, 'before');
}
add_action('wp_enqueue_scripts', 'rodust_scripts');

/**
 * Incluir Nav Walker personalizado para Tailwind
 */
require_once get_template_directory() . '/inc/class-tailwind-nav-walker.php';

/**
 * Configurações de imagem
 */
function rodust_image_sizes() {
    add_image_size('rodust-featured', 800, 400, true);
    add_image_size('rodust-thumbnail', 300, 200, true);
}
add_action('after_setup_theme', 'rodust_image_sizes');

/**
 * Customizer (se necessário no futuro)
 */
function rodust_customize_register($wp_customize) {
    // Adicionar seções do customizer aqui
}
add_action('customize_register', 'rodust_customize_register');

/**
 * Remove generator tag (segurança)
 */
remove_action('wp_head', 'wp_generator');

/**
 * Helper para exibir carousel ou fallback
 */
function rodust_display_carousel($atts = array()) {
    if (function_exists('rodust_carousel')) {
        // Plugin ativo - mostra carousel
        return rodust_carousel($atts);
    } else {
        // Plugin inativo - mostra fallback
        return '
        <div class="carousel-fallback">
            <h3>🎠 Carousel Disponível</h3>
            <p>Ative o plugin <strong>"Rodust Carousel"</strong> para exibir slides nesta área.</p>
            <a href="' . admin_url('plugins.php') . '" class="btn-primary">Gerenciar Plugins</a>
        </div>';
    }
}