</main>

<footer class="text-white" style="background-color: #1d2327;">
    <div class="container-custom py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Coluna 1: Sobre -->
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-xl font-semibold mb-4"><?php bloginfo('name'); ?></h3>
                <p class="text-gray-400 mb-4">
                    <?php
                    $description = get_bloginfo('description');
                    if ($description) {
                        echo esc_html($description);
                    } else {
                        echo 'Sua nova loja de Ferramentas em Piracicaba.';
                    }
                    ?>
                </p>
            </div>
            
            <!-- Coluna 2: Links Rápidos -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class' => 'space-y-2',
                    'container' => 'ul',
                    'fallback_cb' => false,
                    'link_before' => '<span class="text-gray-400 hover:text-white transition-colors">',
                    'link_after' => '</span>',
                ));
                ?>
            </div>
            
            <!-- Coluna 3: Contato -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contato</h3>
                <div class="space-y-2 text-gray-400">
                    <p>contato@rodust.com.br</p>
                    <p>+55 19 99201-5005</p>
                </div>
            </div>
        </div>
        
        <!-- Linha inferior -->
        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Todos os direitos reservados.
            </p>
            
            <div class="mt-4 md:mt-0">
                <p class="text-gray-400 text-sm">
                    Desenvolvido com WordPress | by Aureo
                </p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>