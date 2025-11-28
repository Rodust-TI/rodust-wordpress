<?php get_header(); ?>

<main>
    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="container mx-auto px-4 py-8">
            <?php echo rodust_display_carousel(array(
                'height' => '400px',
                'class' => 'homepage-carousel'
            )); ?>
        </div>
    </section>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-50 to-gray-100 py-20">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Bem-vindo ao <?php bloginfo('name'); ?>
                </h1>
                
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    <?php
                    $description = get_bloginfo('description');
                    if ($description) {
                        echo esc_html($description);
                    } else {
                        echo 'Sua descrição personalizada aqui. Configure em Configurações > Geral.';
                    }
                    ?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#sobre" class="btn-primary text-lg px-8 py-3">
                        Saiba Mais
                    </a>
                    <a href="#contato" class="btn-secondary text-lg px-8 py-3">
                        Entre em Contato
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre Section -->
    <section id="sobre" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Sobre Nós
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                        Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                        Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Duis aute irure dolor in reprehenderit in voluptate velit esse 
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat 
                        cupidatat non proident.
                    </p>
                    <a href="/sobre" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Leia mais sobre nossa história →
                    </a>
                </div>
                
                <div class="aspect-video bg-gray-200 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Imagem ou vídeo aqui</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços/Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Nossos Serviços
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Oferecemos soluções completas e personalizadas para suas necessidades
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Serviço 1 -->
                <div class="bg-white p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Serviço 1</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Descrição do primeiro serviço oferecido pela empresa.
                    </p>
                </div>
                
                <!-- Serviço 2 -->
                <div class="bg-white p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Serviço 2</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Descrição do segundo serviço oferecido pela empresa.
                    </p>
                </div>
                
                <!-- Serviço 3 -->
                <div class="bg-white p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Serviço 3</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Descrição do terceiro serviço oferecido pela empresa.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Posts Recentes -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Últimas Notícias
                </h2>
                <p class="text-gray-600">Fique por dentro das novidades</p>
            </div>
            
            <?php
            $recent_posts = new WP_Query(array(
                'posts_per_page' => 3,
                'post_status' => 'publish'
            ));
            ?>
            
            <?php if ($recent_posts->have_posts()) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                        <article class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="aspect-video overflow-hidden">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('rodust-featured', 'class=w-full h-full object-cover hover:scale-105 transition-transform'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-blue-600 transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 text-sm mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </p>
                                
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span><?php echo get_the_date(); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Leia mais
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="text-center">
                    <p class="text-gray-600">Nenhum post encontrado. Crie seu primeiro post!</p>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contato" class="py-16 bg-blue-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Pronto para começar?
            </h2>
            <p class="text-blue-100 text-xl mb-8 max-w-2xl mx-auto">
                Entre em contato conosco e descubra como podemos ajudar seu negócio a crescer.
            </p>
            <a href="/contato" class="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-8 rounded-lg transition-colors inline-block">
                Fale Conosco
            </a>
        </div>
    </section>
</main>

<?php get_footer(); ?>