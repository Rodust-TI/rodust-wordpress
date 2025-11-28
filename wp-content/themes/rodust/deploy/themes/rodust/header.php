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
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="shadow-lg border-b border-gray-700" style="background-color: #1d2327;">
    <div class="container-custom">
        <nav class="flex justify-between items-center py-4">
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
            </div>
            
            <!-- Botão Menu Mobile -->
            <div class="md:hidden">
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
</header>

<main class="min-h-screen">