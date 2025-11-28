<?php
/**
 * Plugin Name: Rodust Ecommerce
 * Plugin URI: https://rodust.com.br
 * Description: Sistema de e-commerce integrado com API Laravel. Gerencia produtos, carrinho e checkout.
 * Version: 1.0.0
 * Author: Rodust
 * Author URI: https://rodust.com.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rodust-ecommerce
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

// Plugin constants
define('RODUST_ECOMMERCE_VERSION', '1.0.0');
define('RODUST_ECOMMERCE_PATH', plugin_dir_path(__FILE__));
define('RODUST_ECOMMERCE_URL', plugin_dir_url(__FILE__));
define('RODUST_ECOMMERCE_BASENAME', plugin_basename(__FILE__));

// Composer autoloader (se usar dependências)
if (file_exists(RODUST_ECOMMERCE_PATH . 'vendor/autoload.php')) {
    require_once RODUST_ECOMMERCE_PATH . 'vendor/autoload.php';
}

// URL configuration helpers
require_once RODUST_ECOMMERCE_PATH . 'includes/functions-urls.php';

// Helper functions class
require_once RODUST_ECOMMERCE_PATH . 'includes/class-helpers.php';

// Main plugin class
require_once RODUST_ECOMMERCE_PATH . 'includes/class-rodust-ecommerce.php';

/**
 * Initialize the plugin
 */
function rodust_ecommerce() {
    return Rodust_Ecommerce::instance();
}

// Start the plugin
rodust_ecommerce();

/**
 * TODO (v2.0): Auto-criar páginas na ativação do plugin
 * 
 * register_activation_hook(__FILE__, 'rodust_ecommerce_create_pages');
 * 
 * function rodust_ecommerce_create_pages() {
 *     $pages = [
 *         'checkout' => [
 *             'title' => 'Checkout',
 *             'content' => '[rodust_checkout]',
 *             'slug' => 'checkout'
 *         ],
 *         'payment' => [
 *             'title' => 'Pagamento',
 *             'content' => '[rodust_payment]',
 *             'slug' => 'checkout/payment'
 *         ],
 *         'order_confirmation' => [
 *             'title' => 'Pedido Confirmado',
 *             'content' => '[rodust_order_confirmation]',
 *             'slug' => 'pedido-confirmado'
 *         ]
 *     ];
 *     
 *     foreach ($pages as $key => $page) {
 *         // Verificar se página já existe
 *         if (!get_page_by_path($page['slug'])) {
 *             wp_insert_post([
 *                 'post_title' => $page['title'],
 *                 'post_content' => $page['content'],
 *                 'post_status' => 'publish',
 *                 'post_type' => 'page',
 *                 'post_name' => $page['slug']
 *             ]);
 *         }
 *     }
 * }
 * 
 * Benefícios:
 * - Evita erro humano na criação de páginas
 * - Garante shortcodes corretos
 * - Páginas criadas automaticamente ao ativar plugin
 * - Facilita instalação em novos ambientes
 */
