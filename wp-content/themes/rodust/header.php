<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts (opcional - Inter para combinar com Tailwind) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Toast Notification Global -->
    <script>
        // Função global para toast notifications
        window.showToast = function(message, type = 'success') {
            const bgColors = {
                success: '#10b981',
                error: '#ef4444',
                info: '#3b82f6'
            };
            
            const toastHtml = `
                <div style="position: fixed; top: 20px; right: 20px; background: ${bgColors[type]}; color: white; 
                            padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
                            z-index: 10000; font-weight: 500; max-width: 400px;">
                    ${message}
                </div>
            `;
            
            const toast = document.createElement('div');
            toast.innerHTML = toastHtml;
            const toastElement = toast.firstElementChild;
            document.body.appendChild(toastElement);
            
            setTimeout(function() {
                toastElement.style.transition = 'opacity 0.3s';
                toastElement.style.opacity = '0';
                setTimeout(function() {
                    toastElement.remove();
                }, 300);
            }, 5000);
        };
    </script>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="shadow-lg border-b border-gray-700 w-full" style="background-color: #1d2327;">
    <div class="container-custom w-full">
        <nav class="flex justify-between items-center py-4 w-full">
            <!-- Logo à Esquerda -->
            <div class="flex-shrink-0 logo-container">
                <?php if (has_custom_logo()) : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="block">
                        <?php 
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                        if ($logo) : ?>
                            <img 
                                src="<?php echo esc_url($logo[0]); ?>" 
                                alt="<?php bloginfo('name'); ?>" 
                                class="h-12 md:h-16 w-auto transition-all duration-300 hover:scale-105"
                                loading="eager"
                            >
                        <?php endif; ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-2xl font-bold text-white hover:text-blue-400 transition-colors">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Menu Principal à Direita (Desktop) -->
            <div class="hidden md:flex md:items-center md:space-x-1">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => false,
                    'walker' => new Tailwind_Nav_Walker(),
                ));
                ?>
                
                <!-- Ícone do Carrinho -->
                <?php if (get_option('rodust_show_cart_icon', '1') === '1') : ?>
                <a href="<?php echo home_url('/carrinho'); ?>" 
                   class="relative ml-4 text-gray-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="cart-count-badge" 
                          class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">
                        0
                    </span>
                </a>
                <?php endif; ?>
                
                <!-- Ícone do Usuário -->
                <?php if (get_option('rodust_show_user_icon', '1') === '1') : ?>
                <div class="relative ml-2" id="user-menu-desktop">
                    <button 
                        id="user-menu-button"
                        class="flex items-center text-gray-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div 
                        id="user-dropdown"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                        <div id="user-logged-in-menu" class="hidden">
                            <a href="<?php echo home_url('/minha-conta'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                👤 Minha Conta
                            </a>
                            <a href="<?php echo home_url('/minha-conta?tab=pedidos'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                📦 Meus Pedidos
                            </a>
                            <hr class="my-1">
                            <button 
                                id="logout-button"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                🚪 Sair
                            </button>
                        </div>
                        <div id="user-logged-out-menu" class="hidden">
                            <a href="<?php echo home_url('/login'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🔑 Entrar
                            </a>
                            <a href="<?php echo home_url('/cadastro'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                ✍️ Cadastrar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Ícone de Wishlist -->
                <?php if (get_option('rodust_show_wishlist_icon', '0') === '1') : ?>
                <a href="<?php echo home_url('/lista-desejos'); ?>" 
                   class="relative ml-2 text-gray-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span id="wishlist-count-badge" 
                          class="absolute -top-1 -right-1 bg-pink-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">
                        0
                    </span>
                </a>
                <?php endif; ?>
                
                <!-- Ícone de Busca -->
                <?php if (get_option('rodust_show_search_icon', '0') === '1') : ?>
                <button 
                    id="search-toggle-button"
                    class="ml-2 text-gray-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Botão Menu Mobile -->
            <div class="md:hidden flex items-center gap-2">
                <!-- Carrinho Mobile -->
                <?php if (get_option('rodust_show_cart_icon', '1') === '1') : ?>
                <a href="<?php echo home_url('/carrinho'); ?>" 
                   class="relative text-gray-300 hover:text-white transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="cart-count-badge-mobile" 
                          class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">
                        0
                    </span>
                </a>
                <?php endif; ?>
                
                <!-- Usuário Mobile -->
                <?php if (get_option('rodust_show_user_icon', '1') === '1') : ?>
                <div class="relative" id="user-menu-mobile">
                    <button 
                        id="user-menu-button-mobile"
                        class="text-gray-300 hover:text-white transition-colors p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Mobile -->
                    <div 
                        id="user-dropdown-mobile"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                        <div id="user-logged-in-menu-mobile" class="hidden">
                            <a href="<?php echo home_url('/minha-conta'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                👤 Minha Conta
                            </a>
                            <a href="<?php echo home_url('/minha-conta?tab=pedidos'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                📦 Meus Pedidos
                            </a>
                            <hr class="my-1">
                            <button 
                                id="logout-button-mobile"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                🚪 Sair
                            </button>
                        </div>
                        <div id="user-logged-out-menu-mobile" class="hidden">
                            <a href="<?php echo home_url('/login'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🔑 Entrar
                            </a>
                            <a href="<?php echo home_url('/cadastro'); ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                ✍️ Cadastrar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <button 
                    data-mobile-menu-toggle
                    class="text-gray-300 hover:text-white focus:outline-none focus:text-white transition-colors"
                    aria-label="Menu"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </nav>
        
        <!-- Menu Mobile -->
        <div data-mobile-menu class="hidden md:hidden">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'mobile-menu',
                'container' => false,
                'fallback_cb' => false,
                'walker' => new Tailwind_Nav_Walker(),
            ));
            ?>
        </div>
    </div>
    
    <!-- Modal de Busca Global -->
    <?php if (get_option('rodust_show_search_icon', '0') === '1') : ?>
    <div id="search-modal" class="hidden fixed inset-x-0 top-0 z-50 mt-20">
        <div class="container mx-auto px-4">
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden max-w-2xl mx-auto">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            id="global-search-input"
                            placeholder="Buscar produtos..."
                            class="flex-1 text-lg border-none focus:outline-none focus:ring-0"
                            autofocus>
                        <button 
                            id="close-search-modal"
                            class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="search-results" class="max-h-96 overflow-y-auto">
                        <p class="text-gray-500 text-sm">Digite para buscar produtos...</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Backdrop -->
        <div id="search-backdrop" class="fixed inset-0 bg-black bg-opacity-50 -z-10"></div>
    </div>
    <?php endif; ?>
</header>

<main class="min-h-screen">