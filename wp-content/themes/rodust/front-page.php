<?php
/**
 * Template para página inicial estática
 * Usado quando uma página estática é definida como página inicial
 */

get_header(); ?>

<main>
    <!-- Carousel Section -->
    <section class="carousel-section">
        <?php echo rodust_display_carousel(array(
            'height' => '400px',
            'class' => 'homepage-carousel'
        )); ?>
    </section>
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            
            <!-- Hero Section com conteúdo da página -->
            <section class="relative bg-gradient-to-br from-gray-50 to-gray-100 py-20">
                <div class="container mx-auto px-4">
                    <div class="text-center max-w-4xl mx-auto">
                        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                            <?php the_title(); ?>
                        </h1>
                        
                        <?php if (has_excerpt()) : ?>
                            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                                <?php the_excerpt(); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/sobre-nos" class="btn-primary text-lg px-8 py-3">
                                Saiba Mais
                            </a>
                            <a href="/contato" class="btn-secondary text-lg px-8 py-3">
                                Entre em Contato
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Conteúdo da Página -->
            <section id="conteudo" class="py-16 bg-white">
                <div class="container mx-auto px-4">
                    <div class="prose prose-lg mx-auto max-w-4xl">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
            
        <?php endwhile; ?>
    <?php else : ?>
        
        <!-- Fallback caso não tenha conteúdo -->
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
                            echo 'Configure sua página inicial em Configurações > Leitura.';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </section>
        
    <?php endif; ?>

    <!-- Seção adicional sempre presente -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Nossos Destaques
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Qualidade</h3>
                    <p class="text-gray-600">Excelência em todos os nossos serviços e produtos.</p>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Equilíbrio</h3>
                    <p class="text-gray-600">Custo-Benefício equilibrado é interessante para nossos clientes.</p>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Atendimento</h3>
                    <p class="text-gray-600">Ajudando você a encontrar as melhores soluções.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>