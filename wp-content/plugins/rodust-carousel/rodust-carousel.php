<?php
/**
 * Plugin Name: Rodust Carousel
 * Description: Sistema de carousel responsivo com painel admin para configuração de slides, links e imagens.
 * Version: 1.0.0
 * Author: Rodust Theme
 * Text Domain: rodust-carousel
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal do Carousel
 */
class Rodust_Carousel {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Hooks admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Hooks frontend
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_save_carousel_slide', array($this, 'save_slide'));
        add_action('wp_ajax_delete_carousel_slide', array($this, 'delete_slide'));
        add_action('wp_ajax_reorder_carousel_slides', array($this, 'reorder_slides'));
        
        // Shortcode
        add_shortcode('rodust_carousel', array($this, 'render_carousel'));
    }
    
    /**
     * Adiciona menu no admin
     */
    public function add_admin_menu() {
        add_menu_page(
            'Gerenciar Slides',
            'Carousel',
            'manage_options',
            'rodust-carousel',
            array($this, 'admin_page'),
            'dashicons-images-alt2',
            30
        );
        
        add_submenu_page(
            'rodust-carousel',
            'Configurações do Carousel',
            'Configurações',
            'manage_options',
            'rodust-carousel-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Registra configurações
     */
    public function register_settings() {
        register_setting('rodust_carousel_group', 'rodust_carousel_settings');
    }
    
    
    /**
     * Scripts do admin
     */
    public function admin_scripts($hook) {
        if ($hook !== 'toplevel_page_rodust-carousel' && $hook !== 'carousel_page_rodust-carousel-settings') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('rodust-carousel-admin', plugin_dir_url(__FILE__) . 'admin.css', array(), '1.0.0');
        wp_enqueue_script('rodust-carousel-admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery', 'jquery-ui-sortable'), '1.0.0', true);
        
        wp_localize_script('rodust-carousel-admin', 'carousel_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('carousel_nonce')
        ));
    }
    
    /**
     * Scripts do frontend
     */
    public function frontend_scripts() {
        wp_enqueue_style('rodust-carousel-front', plugin_dir_url(__FILE__) . 'carousel.css', array(), '1.0.0');
        wp_enqueue_script('rodust-carousel-front', plugin_dir_url(__FILE__) . 'carousel.js', array('jquery'), '1.0.0', true);
    }
    
    /**
     * Página do admin
     */
    public function admin_page() {
        $slides = get_option('rodust_carousel_slides', array());
        include plugin_dir_path(__FILE__) . 'admin-page.php';
    }

    /**
     * Página de configurações
     */
    public function settings_page() {
        $settings = get_option('rodust_carousel_settings', array(
            'autoplay' => true,
            'autoplay_speed' => 5000,
            'show_dots' => true,
            'show_arrows' => true
        ));
        
        include plugin_dir_path(__FILE__) . 'settings-page.php';
    }
    
    /**
     * Salva slide via AJAX
     */
    public function save_slide() {
        check_ajax_referer('carousel_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $slide_data = array(
            'id' => sanitize_text_field($_POST['slide_id']),
            'title' => sanitize_text_field($_POST['title']),
            'image' => esc_url_raw($_POST['image']),
            'link' => esc_url_raw($_POST['link']),
            'link_text' => sanitize_text_field($_POST['link_text']),
            'description' => sanitize_textarea_field($_POST['description']),
            'order' => intval($_POST['order'])
        );
        
        $slides = get_option('rodust_carousel_slides', array());

        // Garante que $slides seja sempre um array para evitar erros fatais
        if (!is_array($slides)) {
            $slides = array();
        }
        
        if (empty($slide_data['id'])) {
            $slide_data['id'] = uniqid('slide_');
        }
        
        $slides[$slide_data['id']] = $slide_data;
        update_option('rodust_carousel_slides', $slides);
        
        wp_send_json_success($slide_data);
    }
    
    /**
     * Delete slide via AJAX
     */
    public function delete_slide() {
        check_ajax_referer('carousel_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $slide_id = sanitize_text_field($_POST['slide_id']);
        $slides = get_option('rodust_carousel_slides', array());
        
        if (isset($slides[$slide_id])) {
            unset($slides[$slide_id]);
            update_option('rodust_carousel_slides', $slides);
        }
        
        wp_send_json_success();
    }
    
    /**
     * Reordena slides via AJAX
     */
    public function reorder_slides() {
        check_ajax_referer('carousel_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $order = array_map('sanitize_text_field', $_POST['order']);
        $slides = get_option('rodust_carousel_slides', array());
        
        foreach ($order as $index => $slide_id) {
            if (isset($slides[$slide_id])) {
                $slides[$slide_id]['order'] = $index;
            }
        }
        
        update_option('rodust_carousel_slides', $slides);
        wp_send_json_success();
    }
    
    /**
     * Renderiza o carousel
     */
    public function render_carousel($atts) {
        $atts = shortcode_atts(array(
            'height' => '300px',
            'class' => ''
        ), $atts);
        
        $slides = get_option('rodust_carousel_slides', array());
        $settings = get_option('rodust_carousel_settings', array());
        
        if (empty($slides)) {
            return '<p>Nenhum slide configurado. <a href="' . admin_url('admin.php?page=rodust-carousel') . '">Configure aqui</a>.</p>';
        }
        
        // Ordena slides
        uasort($slides, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'carousel-template.php';
        return ob_get_clean();
    }
}

// Inicializa o plugin
new Rodust_Carousel();

/**
 * Função helper para usar no tema
 */
if (!function_exists('rodust_carousel')) {
    function rodust_carousel($atts = array()) {
        return do_shortcode('[rodust_carousel ' . http_build_query($atts, '', ' ') . ']');
    }
}
?>