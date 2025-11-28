<?php
/**
 * Plugin Name: Smart Menu Links
 * Description: Permite usar links inteligentes nos menus - use 'home' para página inicial ou apenas o slug da página (ex: 'produtos' para ir para /produtos)
 * Version: 1.0.0
 * Author: Rodust Theme
 * Text Domain: smart-menu-links
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal do plugin
 */
class Smart_Menu_Links {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Hook para processar URLs dos menus antes de renderizar
        add_filter('wp_nav_menu_objects', array($this, 'process_smart_links'), 10, 2);
        
        // Hook para salvar menus (permite salvar links "inválidos")
        add_action('wp_update_nav_menu_item', array($this, 'save_smart_links'), 10, 3);
        
        // Adicionar help text no admin de menus
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }
    
    /**
     * Processa os links inteligentes nos menus
     */
    public function process_smart_links($items, $args) {
        foreach ($items as &$item) {
            if ($item->type === 'custom') {
                $item->url = $this->convert_smart_link($item->url);
            }
        }
        return $items;
    }
    
    /**
     * Permite salvar links inteligentes (bypass da validação do WP)
     */
    public function save_smart_links($menu_id, $menu_item_db_id, $args) {
        // Se o campo URL contém um smart link, permitir salvar
        if (isset($args['menu-item-url']) && !empty($args['menu-item-url'])) {
            $url = sanitize_text_field($args['menu-item-url']);
            
            // Lista de smart links permitidos
            $smart_links = array('home', 'inicio', 'inicial', 'blog', 'contato', 'contact', 'sobre', 'about');
            
            // Se é um smart link conhecido ou um slug simples
            if (in_array(strtolower($url), $smart_links) || !filter_var($url, FILTER_VALIDATE_URL)) {
                // Força a URL a ser salva mesmo sendo "inválida"
                update_post_meta($menu_item_db_id, '_menu_item_url', $url);
            }
        }
    }
    
    /**
     * Converte links inteligentes em URLs reais
     */
    public function convert_smart_link($url) {
        // Remove espaços e converte para minúsculo
        $url = trim(strtolower($url));
        
        // Se já é uma URL completa, retorna sem modificar
        if (filter_var($url, FILTER_VALIDATE_URL) || strpos($url, 'http') === 0) {
            return $url;
        }
        
        // Se começa com #, é âncora - mantém como está
        if (strpos($url, '#') === 0) {
            return $url;
        }
        
        // Casos especiais
        switch ($url) {
            case 'home':
            case 'inicio':
            case 'inicial':
                return home_url('/');
                
            case 'blog':
                $blog_page = get_option('page_for_posts');
                if ($blog_page) {
                    return get_permalink($blog_page);
                }
                return home_url('/blog/');
                
            case 'contato':
            case 'contact':
                return $this->find_page_by_slug(['contato', 'contact', 'fale-conosco']);
                
            case 'sobre':
            case 'about':
                return $this->find_page_by_slug(['sobre', 'about', 'sobre-nos', 'quem-somos']);
                
            default:
                // Tenta encontrar uma página com o slug fornecido
                return $this->find_page_by_slug($url);
        }
    }
    
    /**
     * Busca página por slug(s)
     */
    private function find_page_by_slug($slugs) {
        if (!is_array($slugs)) {
            $slugs = array($slugs);
        }
        
        foreach ($slugs as $slug) {
            // Busca página
            $page = get_page_by_path($slug);
            if ($page) {
                return get_permalink($page->ID);
            }
            
            // Busca post
            $post = get_page_by_path($slug, OBJECT, 'post');
            if ($post) {
                return get_permalink($post->ID);
            }
            
            // Busca custom post types
            $post_types = get_post_types(array('public' => true, '_builtin' => false));
            foreach ($post_types as $post_type) {
                $post = get_page_by_path($slug, OBJECT, $post_type);
                if ($post) {
                    return get_permalink($post->ID);
                }
            }
        }
        
        // Se não encontrou nada, cria URL baseada no slug
        return home_url('/' . sanitize_title($slugs[0]) . '/');
    }
    
    /**
     * Adiciona scripts no admin para mostrar help
     */
    public function admin_scripts($hook) {
        if ($hook !== 'nav-menus.php') {
            return;
        }
        
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {
                // Adiciona help text nos campos de URL customizado
                $('#menu-to-edit').on('focus', '.edit-menu-item-url', function() {
                    var helpText = $(this).next('.smart-links-help');
                    if (helpText.length === 0) {
                        $(this).after('<div class=\"smart-links-help\" style=\"font-size: 11px; color: #666; margin-top: 3px;\">💡 Use: <strong>home</strong> (página inicial), <strong>produtos</strong> (slug da página), <strong>contato</strong>, <strong>sobre</strong>, etc.</div>');
                    }
                });
            });
        ");
    }
}

// Inicializa o plugin
new Smart_Menu_Links();

/**
 * Função helper para usar em templates (opcional)
 */
if (!function_exists('smart_link')) {
    function smart_link($slug) {
        $plugin = new Smart_Menu_Links();
        return $plugin->convert_smart_link($slug);
    }
}
?>