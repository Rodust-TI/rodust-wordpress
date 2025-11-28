<?php
/**
 * Product Custom Post Type
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

/**
 * Register and manage product custom post type
 */
class Rodust_Product_Post_Type {
    
    /**
     * Instance
     *
     * @var Rodust_Product_Post_Type
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', [__CLASS__, 'register']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_rodust_product', [$this, 'save_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_rodust_sync_product', [$this, 'ajax_sync_product']);
        add_action('wp_ajax_rodust_load_product_data', [$this, 'ajax_load_product_data']);
    }

    /**
     * Register product post type
     */
    public static function register() {
        $labels = [
            'name' => __('Produtos', 'rodust-ecommerce'),
            'singular_name' => __('Produto', 'rodust-ecommerce'),
            'menu_name' => __('Produtos', 'rodust-ecommerce'),
            'add_new' => __('Adicionar Novo', 'rodust-ecommerce'),
            'add_new_item' => __('Adicionar Novo Produto', 'rodust-ecommerce'),
            'edit_item' => __('Editar Produto', 'rodust-ecommerce'),
            'new_item' => __('Novo Produto', 'rodust-ecommerce'),
            'view_item' => __('Ver Produto', 'rodust-ecommerce'),
            'search_items' => __('Buscar Produtos', 'rodust-ecommerce'),
            'not_found' => __('Nenhum produto encontrado', 'rodust-ecommerce'),
            'all_items' => __('Todos os Produtos', 'rodust-ecommerce'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'produtos'],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'dashicons-cart',
            'menu_position' => 20,
            'show_in_rest' => true,
            'taxonomies' => [],
        ];

        register_post_type('rodust_product', $args);
    }

    /**
     * Register product taxonomies
     */
    public function register_taxonomies() {
        // Categoria de Produto
        register_taxonomy('product_category', 'rodust_product', [
            'label' => __('Categorias', 'rodust-ecommerce'),
            'rewrite' => ['slug' => 'categoria-produto'],
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ]);

        // Tags de Produto
        register_taxonomy('product_tag', 'rodust_product', [
            'label' => __('Tags', 'rodust-ecommerce'),
            'rewrite' => ['slug' => 'tag-produto'],
            'hierarchical' => false,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ]);

        // Marca
        register_taxonomy('product_brand', 'rodust_product', [
            'label' => __('Marcas', 'rodust-ecommerce'),
            'rewrite' => ['slug' => 'marca'],
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ]);

        // Tipo de Ferramenta (específico para loja de mecânico)
        register_taxonomy('tool_type', 'rodust_product', [
            'label' => __('Tipos de Ferramenta', 'rodust-ecommerce'),
            'rewrite' => ['slug' => 'tipo-ferramenta'],
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
        ]);
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'rodust_product_data',
            __('Dados do Produto', 'rodust-ecommerce'),
            [$this, 'render_product_data_meta_box'],
            'rodust_product',
            'normal',
            'high'
        );

        add_meta_box(
            'rodust_product_shipping',
            __('Dimensões e Frete', 'rodust-ecommerce'),
            [$this, 'render_shipping_meta_box'],
            'rodust_product',
            'normal',
            'high'
        );

        add_meta_box(
            'rodust_product_commercial',
            __('IDs de Integração', 'rodust-ecommerce'),
            [$this, 'render_commercial_meta_box'],
            'rodust_product',
            'normal',
            'default'
        );

        add_meta_box(
            'rodust_product_gallery',
            __('Galeria de Imagens (até 3 fotos)', 'rodust-ecommerce'),
            [$this, 'render_gallery_meta_box'],
            'rodust_product',
            'side',
            'default'
        );

        add_meta_box(
            'rodust_product_sync',
            __('Sincronização', 'rodust-ecommerce'),
            [$this, 'render_sync_meta_box'],
            'rodust_product',
            'side',
            'default'
        );
    }

    /**
     * Render product data meta box
     */
    public function render_product_data_meta_box($post) {
        wp_nonce_field('rodust_product_data', 'rodust_product_nonce');

        $sku = get_post_meta($post->ID, '_sku', true);
        $price = get_post_meta($post->ID, '_price', true);
        $promotional_price = get_post_meta($post->ID, '_promotional_price', true);
        $stock = get_post_meta($post->ID, '_stock', true);
        $laravel_id = get_post_meta($post->ID, '_laravel_id', true);
        ?>
        <!-- Campo hidden para JavaScript detectar Laravel ID -->
        <input type="hidden" name="product_laravel_id" value="<?php echo esc_attr($laravel_id); ?>">
        
        <style>
            .rodust-data-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 10px;
            }
            .rodust-data-grid .form-field {
                margin-bottom: 0;
            }
            .rodust-data-grid label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .rodust-data-grid input[type="text"],
            .rodust-data-grid input[type="number"] {
                width: 100%;
                max-width: 300px;
            }
            .rodust-data-grid .description {
                margin-top: 5px;
                font-size: 13px;
                color: #646970;
            }
        </style>
        
        <div class="rodust-data-grid">
            <div class="form-field">
                <label for="product_sku"><?php _e('SKU', 'rodust-ecommerce'); ?></label>
                <input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($sku); ?>" class="regular-text">
            </div>
            
            <div class="form-field">
                <label for="product_stock"><?php _e('Estoque', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_stock" name="product_stock" value="<?php echo esc_attr($stock); ?>" min="0" class="small-text">
            </div>
            
            <div class="form-field">
                <label for="product_price"><?php _e('Preço Normal (R$)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" class="small-text">
                <p class="description"><?php _e('Preço regular do produto', 'rodust-ecommerce'); ?></p>
            </div>
            
            <div class="form-field">
                <label for="product_promotional_price"><?php _e('Preço Promocional (R$)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_promotional_price" name="product_promotional_price" value="<?php echo esc_attr($promotional_price); ?>" step="0.01" min="0" class="small-text">
                <p class="description"><?php _e('Deixe vazio se não houver promoção', 'rodust-ecommerce'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render shipping dimensions meta box
     */
    public function render_shipping_meta_box($post) {
        wp_nonce_field('rodust_product_shipping', 'rodust_shipping_nonce');

        $width = get_post_meta($post->ID, '_width', true);
        $height = get_post_meta($post->ID, '_height', true);
        $length = get_post_meta($post->ID, '_length', true);
        $weight = get_post_meta($post->ID, '_weight', true);
        $free_shipping = get_post_meta($post->ID, '_free_shipping', true);
        $brand = get_post_meta($post->ID, '_brand', true);
        
        // Buscar marca associada ao produto
        $brands = wp_get_post_terms($post->ID, 'product_brand');
        $current_brand = !empty($brands) ? $brands[0]->name : '';
        ?>
        <div style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 15px;">
            <strong>⚠️ Importante:</strong> Estas dimensões são utilizadas para o cálculo automático de frete.
        </div>
        
        <style>
            .rodust-dimensions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
            .rodust-dimension-field { display: flex; flex-direction: column; }
            .rodust-dimension-field label { font-weight: 600; margin-bottom: 5px; }
            .rodust-dimension-field input { width: 100%; }
            .rodust-dimension-field .description { margin-top: 3px; font-size: 12px; color: #666; }
        </style>
        
        <div class="rodust-dimensions-grid">
            <div class="rodust-dimension-field">
                <label for="product_width"><?php _e('Largura (cm)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_width" name="product_width" value="<?php echo esc_attr($width); ?>" step="0.01" min="0" class="regular-text">
                <span class="description"><?php _e('Largura da embalagem', 'rodust-ecommerce'); ?></span>
            </div>
            
            <div class="rodust-dimension-field">
                <label for="product_height"><?php _e('Altura (cm)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_height" name="product_height" value="<?php echo esc_attr($height); ?>" step="0.01" min="0" class="regular-text">
                <span class="description"><?php _e('Altura da embalagem', 'rodust-ecommerce'); ?></span>
            </div>
            
            <div class="rodust-dimension-field">
                <label for="product_length"><?php _e('Comprimento (cm)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_length" name="product_length" value="<?php echo esc_attr($length); ?>" step="0.01" min="0" class="regular-text">
                <span class="description"><?php _e('Comprimento da embalagem', 'rodust-ecommerce'); ?></span>
            </div>
            
            <div class="rodust-dimension-field">
                <label for="product_weight"><?php _e('Peso (kg)', 'rodust-ecommerce'); ?></label>
                <input type="number" id="product_weight" name="product_weight" value="<?php echo esc_attr($weight); ?>" step="0.001" min="0" class="regular-text">
                <span class="description"><?php _e('Peso total do produto', 'rodust-ecommerce'); ?></span>
            </div>
        </div>
        
        <table class="form-table" style="margin-top: 15px;">
            <tr>
                <th style="width: 150px;"><label><?php _e('Marca/Fabricante', 'rodust-ecommerce'); ?></label></th>
                <td>
                    <input type="text" id="product_brand_display" value="<?php echo esc_attr($current_brand); ?>" class="regular-text rodust-readonly" readonly>
                    <input type="hidden" name="product_brand" value="<?php echo esc_attr($brand); ?>">
                    <p class="description rodust-data-source">📊 Marca vinda do Bling (somente leitura)</p>
                </td>
            </tr>
            <tr>
                <th><label for="free_shipping"><?php _e('Frete Grátis', 'rodust-ecommerce'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="free_shipping" name="free_shipping" value="1" <?php checked($free_shipping, '1'); ?> class="rodust-readonly" disabled>
                        <?php _e('Este produto tem frete grátis', 'rodust-ecommerce'); ?>
                    </label>
                    <p class="description rodust-data-source">📊 Configuração vinda do Bling (somente leitura)</p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render commercial info meta box
     */
    public function render_commercial_meta_box($post) {
        wp_nonce_field('rodust_product_commercial', 'rodust_commercial_nonce');

        $laravel_id = get_post_meta($post->ID, '_laravel_id', true);
        $bling_id = get_post_meta($post->ID, '_bling_id', true);
        $wordpress_post_id = $post->ID;
        ?>
        <table class="form-table">
            <?php if ($laravel_id): ?>
            <tr>
                <th><?php _e('ID Laravel', 'rodust-ecommerce'); ?></th>
                <td>
                    <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html($laravel_id); ?></code>
                    <p class="description"><?php _e('ID do produto no banco de dados Laravel', 'rodust-ecommerce'); ?></p>
                </td>
            </tr>
            <?php endif; ?>
            
            <?php if ($bling_id): ?>
            <tr>
                <th><?php _e('ID Bling (ERP)', 'rodust-ecommerce'); ?></th>
                <td>
                    <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html($bling_id); ?></code>
                    <p class="description"><?php _e('ID do produto no sistema Bling (previne duplicação)', 'rodust-ecommerce'); ?></p>
                </td>
            </tr>
            <?php endif; ?>
            
            <tr>
                <th><?php _e('ID WordPress', 'rodust-ecommerce'); ?></th>
                <td>
                    <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html($wordpress_post_id); ?></code>
                    <p class="description"><?php _e('ID do post no WordPress', 'rodust-ecommerce'); ?></p>
                </td>
            </tr>
            
            <?php if (!$laravel_id && !$bling_id): ?>
            <tr>
                <td colspan="2">
                    <p style="color: #d63638;">
                        <span class="dashicons dashicons-warning" style="vertical-align: middle;"></span>
                        <?php _e('Este produto ainda não foi sincronizado com o Laravel/Bling.', 'rodust-ecommerce'); ?>
                    </p>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }

    /**
     * Render gallery meta box
     */
    public function render_gallery_meta_box($post) {
        wp_nonce_field('rodust_product_gallery', 'rodust_gallery_nonce');
        
        $gallery = get_post_meta($post->ID, '_product_gallery', true);
        $gallery_ids = $gallery ? explode(',', $gallery) : [];
        ?>
        <div class="rodust-product-gallery-meta">
            <p><?php _e('Adicione até 3 imagens para a galeria do produto (além da imagem destacada).', 'rodust-ecommerce'); ?></p>
            
            <div id="rodust-gallery-images" style="margin-bottom: 15px;">
                <?php foreach ($gallery_ids as $index => $image_id): 
                    if (!$image_id) continue;
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image):
                ?>
                <div class="gallery-image-item" style="display: inline-block; margin: 5px; position: relative;">
                    <img src="<?php echo esc_url($image[0]); ?>" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd;">
                    <button type="button" class="button-link remove-gallery-image" data-image-id="<?php echo $image_id; ?>" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 18px; cursor: pointer;">×</button>
                </div>
                <?php 
                    endif;
                endforeach; ?>
            </div>

            <input type="hidden" id="rodust_gallery_ids" name="rodust_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>">
            
            <button type="button" class="button button-secondary" id="rodust-add-gallery-image">
                <?php _e('Adicionar Imagem', 'rodust-ecommerce'); ?>
            </button>
            
            <p class="description">
                <?php printf(__('Total: %d de 3 imagens', 'rodust-ecommerce'), count($gallery_ids)); ?>
            </p>
        </div>

        <script>
        jQuery(function($) {
            var galleryFrame;
            var galleryIds = $('#rodust_gallery_ids').val() ? $('#rodust_gallery_ids').val().split(',') : [];

            // Adicionar imagem
            $('#rodust-add-gallery-image').on('click', function(e) {
                e.preventDefault();

                if (galleryIds.filter(id => id).length >= 3) {
                    alert('<?php _e('Você já adicionou o máximo de 3 imagens!', 'rodust-ecommerce'); ?>');
                    return;
                }

                if (galleryFrame) {
                    galleryFrame.open();
                    return;
                }

                galleryFrame = wp.media({
                    title: '<?php _e('Selecionar Imagem da Galeria', 'rodust-ecommerce'); ?>',
                    button: {
                        text: '<?php _e('Adicionar à Galeria', 'rodust-ecommerce'); ?>'
                    },
                    multiple: false
                });

                galleryFrame.on('select', function() {
                    var attachment = galleryFrame.state().get('selection').first().toJSON();
                    
                    if (galleryIds.filter(id => id).length >= 3) {
                        alert('<?php _e('Você já adicionou o máximo de 3 imagens!', 'rodust-ecommerce'); ?>');
                        return;
                    }

                    galleryIds.push(attachment.id);
                    
                    var html = '<div class="gallery-image-item" style="display: inline-block; margin: 5px; position: relative;">';
                    html += '<img src="' + attachment.sizes.thumbnail.url + '" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd;">';
                    html += '<button type="button" class="button-link remove-gallery-image" data-image-id="' + attachment.id + '" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 18px; cursor: pointer;">×</button>';
                    html += '</div>';
                    
                    $('#rodust-gallery-images').append(html);
                    $('#rodust_gallery_ids').val(galleryIds.join(','));
                    updateGalleryCount();
                });

                galleryFrame.open();
            });

            // Remover imagem
            $(document).on('click', '.remove-gallery-image', function() {
                var imageId = $(this).data('image-id').toString();
                galleryIds = galleryIds.filter(id => id != imageId);
                $(this).closest('.gallery-image-item').remove();
                $('#rodust_gallery_ids').val(galleryIds.join(','));
                updateGalleryCount();
            });

            function updateGalleryCount() {
                var count = galleryIds.filter(id => id).length;
                $('.rodust-product-gallery-meta .description').text('Total: ' + count + ' de 3 imagens');
            }
        });
        </script>
        <?php
    }

    /**
     * Render sync meta box
     */
    public function render_sync_meta_box($post) {
        $synced_at = get_post_meta($post->ID, '_synced_at', true);
        $laravel_id = get_post_meta($post->ID, '_laravel_id', true);
        ?>
        <div class="rodust-sync-status">
            <?php if ($laravel_id): ?>
                <p><strong><?php _e('Status:', 'rodust-ecommerce'); ?></strong> 
                    <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                    <?php _e('Sincronizado', 'rodust-ecommerce'); ?>
                </p>
                <?php if ($synced_at): ?>
                    <p><small><?php printf(__('Última sincronização: %s', 'rodust-ecommerce'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $synced_at)); ?></small></p>
                <?php endif; ?>
                <button type="button" class="button button-secondary rodust-sync-now" data-product-id="<?php echo $post->ID; ?>">
                    <?php _e('Sincronizar Agora', 'rodust-ecommerce'); ?>
                </button>
            <?php else: ?>
                <p><strong><?php _e('Status:', 'rodust-ecommerce'); ?></strong> 
                    <span class="dashicons dashicons-warning" style="color: orange;"></span>
                    <?php _e('Não sincronizado', 'rodust-ecommerce'); ?>
                </p>
                <p><small><?php _e('Salve o produto para sincronizar com Laravel.', 'rodust-ecommerce'); ?></small></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save product meta
     */
    public function save_meta($post_id, $post) {
        // Security checks for product data
        if (isset($_POST['rodust_product_nonce']) && wp_verify_nonce($_POST['rodust_product_nonce'], 'rodust_product_data')) {
            if (!defined('DOING_AUTOSAVE') || !DOING_AUTOSAVE) {
                if (current_user_can('edit_post', $post_id)) {
                    // Save basic fields
                    if (isset($_POST['product_sku'])) {
                        update_post_meta($post_id, '_sku', sanitize_text_field($_POST['product_sku']));
                    }

                    if (isset($_POST['product_price'])) {
                        update_post_meta($post_id, '_price', floatval($_POST['product_price']));
                    }

                    if (isset($_POST['product_promotional_price'])) {
                        $promo_price = floatval($_POST['product_promotional_price']);
                        update_post_meta($post_id, '_promotional_price', $promo_price > 0 ? $promo_price : '');
                    }

                    if (isset($_POST['product_stock'])) {
                        update_post_meta($post_id, '_stock', intval($_POST['product_stock']));
                    }
                }
            }
        }

        // Security checks for shipping dimensions
        if (isset($_POST['rodust_shipping_nonce']) && wp_verify_nonce($_POST['rodust_shipping_nonce'], 'rodust_product_shipping')) {
            if (!defined('DOING_AUTOSAVE') || !DOING_AUTOSAVE) {
                if (current_user_can('edit_post', $post_id)) {
                    // Save dimensions
                    if (isset($_POST['product_width'])) {
                        update_post_meta($post_id, '_product_width', floatval($_POST['product_width']));
                    }

                    if (isset($_POST['product_height'])) {
                        update_post_meta($post_id, '_product_height', floatval($_POST['product_height']));
                    }

                    if (isset($_POST['product_length'])) {
                        update_post_meta($post_id, '_product_length', floatval($_POST['product_length']));
                    }

                    if (isset($_POST['product_weight'])) {
                        update_post_meta($post_id, '_product_weight', floatval($_POST['product_weight']));
                    }

                    // Save free shipping
                    $free_shipping = isset($_POST['free_shipping']) ? '1' : '';
                    update_post_meta($post_id, '_free_shipping', $free_shipping);
                }
            }
        }

        // Security checks for commercial info
        if (isset($_POST['rodust_commercial_nonce']) && wp_verify_nonce($_POST['rodust_commercial_nonce'], 'rodust_product_commercial')) {
            if (!defined('DOING_AUTOSAVE') || !DOING_AUTOSAVE) {
                if (current_user_can('edit_post', $post_id)) {
                    // Save brand (taxonomy)
                    if (isset($_POST['product_brand'])) {
                        $brand_id = intval($_POST['product_brand']);
                        if ($brand_id > 0) {
                            wp_set_post_terms($post_id, [$brand_id], 'product_brand');
                        } else {
                            wp_set_post_terms($post_id, [], 'product_brand');
                        }
                    }
                }
            }
        }

        // Security checks for gallery
        if (isset($_POST['rodust_gallery_nonce']) && wp_verify_nonce($_POST['rodust_gallery_nonce'], 'rodust_product_gallery')) {
            if (!defined('DOING_AUTOSAVE') || !DOING_AUTOSAVE) {
                if (current_user_can('edit_post', $post_id)) {
                    if (isset($_POST['rodust_gallery_ids'])) {
                        $gallery_ids = sanitize_text_field($_POST['rodust_gallery_ids']);
                        // Limitar a 3 imagens
                        $ids_array = array_filter(explode(',', $gallery_ids));
                        if (count($ids_array) > 3) {
                            $ids_array = array_slice($ids_array, 0, 3);
                        }
                        update_post_meta($post_id, '_product_gallery', implode(',', $ids_array));
                    }
                }
            }
        }

        // Trigger sync with Laravel (handled by Rodust_Product_Sync class)
        do_action('rodust_product_saved', $post_id);
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        
        if (('post.php' === $hook || 'post-new.php' === $hook) && 'rodust_product' === $post_type) {
            wp_enqueue_script(
                'rodust-product-admin',
                plugins_url('../assets/js/product-admin.js', __FILE__),
                ['jquery'],
                '1.0.1',
                true
            );
            
            wp_localize_script('rodust-product-admin', 'rodustProductAdmin', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('rodust_product_admin'),
                'laravel_api_url' => defined('LARAVEL_API_URL') ? LARAVEL_API_URL : 'http://localhost:8000/api',
                'strings' => [
                    'syncing' => __('Sincronizando...', 'rodust-ecommerce'),
                    'sync_success' => __('Produto sincronizado com sucesso!', 'rodust-ecommerce'),
                    'sync_error' => __('Erro ao sincronizar produto.', 'rodust-ecommerce'),
                    'loading' => __('Carregando dados do Laravel...', 'rodust-ecommerce'),
                    'load_error' => __('Erro ao carregar dados do Laravel.', 'rodust-ecommerce'),
                ]
            ]);
            
            // Adicionar CSS inline para campos read-only
            wp_enqueue_style('wp-admin');
            wp_add_inline_style('wp-admin', '
                .rodust-readonly { 
                    background-color: #f5f5f5 !important; 
                    cursor: not-allowed !important; 
                    border-color: #ddd !important;
                }
                .rodust-data-source { 
                    font-size: 11px; 
                    color: #666; 
                    font-style: italic; 
                    margin-top: 2px; 
                }
                .rodust-loading { 
                    opacity: 0.5; 
                }
                .rodust-sync-status .dashicons {
                    vertical-align: middle;
                }
            ');
        }
    }

    /**
     * Ajax: Sync single product with Laravel
     */
    public function ajax_sync_product() {
        check_ajax_referer('rodust_product_admin', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permissão negada']);
        }
        
        $product_id = intval($_POST['product_id']);
        $laravel_id = get_post_meta($product_id, '_laravel_id', true);
        
        if (!$laravel_id) {
            wp_send_json_error(['message' => 'Produto não está vinculado ao Laravel']);
        }
        
        // Buscar dados do Laravel
        $api_client = new Rodust_API_Client();
        $response = $api_client->get("/products/{$laravel_id}");
        
        if (!$response['success']) {
            wp_send_json_error(['message' => 'Erro ao buscar dados do Laravel: ' . ($response['error'] ?? 'Desconhecido')]);
        }
        
        $product_data = $response['data'];
        
        // Atualizar meta fields locais
        update_post_meta($product_id, '_price', floatval($product_data['price'] ?? 0));
        update_post_meta($product_id, '_stock', intval($product_data['stock'] ?? 0));
        update_post_meta($product_id, '_weight', floatval($product_data['weight'] ?? 0));
        update_post_meta($product_id, '_length', floatval($product_data['length'] ?? 0));
        update_post_meta($product_id, '_width', floatval($product_data['width'] ?? 0));
        update_post_meta($product_id, '_height', floatval($product_data['height'] ?? 0));
        update_post_meta($product_id, '_synced_at', time());
        
        // Atribuir marca à taxonomia (se existir)
        if (!empty($product_data['brand'])) {
            $this->assign_brand_taxonomy($product_id, $product_data['brand']);
        }
        
        wp_send_json_success([
            'message' => 'Produto sincronizado com sucesso!',
            'data' => $product_data
        ]);
    }
    
    /**
     * Assign brand to product taxonomy, creating term if doesn't exist
     */
    private function assign_brand_taxonomy($product_id, $brand_name) {
        if (empty($brand_name)) {
            return;
        }
        
        $brand_name = trim($brand_name);
        
        // Verificar se o termo já existe
        $term = term_exists($brand_name, 'product_brand');
        
        // Se não existe, criar
        if (!$term) {
            $term = wp_insert_term($brand_name, 'product_brand', [
                'slug' => sanitize_title($brand_name)
            ]);
            
            if (is_wp_error($term)) {
                error_log('Erro ao criar termo de marca: ' . $term->get_error_message());
                return;
            }
        }
        
        // Obter o term_id
        $term_id = is_array($term) ? $term['term_id'] : $term;
        
        // Atribuir a marca ao produto
        $result = wp_set_post_terms($product_id, [$term_id], 'product_brand', false);
        
        if (is_wp_error($result)) {
            error_log('Erro ao atribuir marca ao produto: ' . $result->get_error_message());
        }
    }

    /**
     * Ajax: Load product data from Laravel
     */
    public function ajax_load_product_data() {
        check_ajax_referer('rodust_product_admin', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permissão negada']);
        }
        
        $product_id = intval($_POST['product_id']);
        $laravel_id = get_post_meta($product_id, '_laravel_id', true);
        
        if (!$laravel_id) {
            wp_send_json_error(['message' => 'Produto não está vinculado ao Laravel']);
        }
        
        // Buscar dados do Laravel
        $api_client = new Rodust_API_Client();
        $response = $api_client->get("/products/{$laravel_id}");
        
        if (!$response['success']) {
            wp_send_json_error(['message' => 'Erro ao buscar dados do Laravel: ' . ($response['error'] ?? 'Desconhecido')]);
        }
        
        wp_send_json_success(['data' => $response['data']]);
    }
}
