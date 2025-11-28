<?php
/**
 * Search Configuration - Force search to products only
 * 
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Search_Config {
    
    public function __construct() {
        // Force search to only products
        add_filter('pre_get_posts', [$this, 'search_only_products']);
        
        // Modify search form
        add_filter('get_search_form', [$this, 'custom_search_form']);
    }
    
    /**
     * Force search to only rodust_product post type
     */
    public function search_only_products($query) {
        if (!is_admin() && $query->is_search() && $query->is_main_query()) {
            $query->set('post_type', 'rodust_product');
        }
        return $query;
    }
    
    /**
     * Custom search form HTML
     */
    public function custom_search_form($form) {
        $form = '<form role="search" method="get" class="search-form relative" action="' . home_url('/') . '">
            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
                <input type="search" 
                       class="flex-1 px-4 py-2 text-gray-900 placeholder-gray-500 focus:outline-none" 
                       placeholder="' . esc_attr__('Buscar produtos...', 'rodust-ecommerce') . '" 
                       value="' . get_search_query() . '" 
                       name="s" />
                <input type="hidden" name="post_type" value="rodust_product" />
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>';
        return $form;
    }
}

// Initialize
new Rodust_Search_Config();
