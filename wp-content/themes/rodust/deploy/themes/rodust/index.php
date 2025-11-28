<?php get_header(); ?>

<main class="container mx-auto px-4 py-8">
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="aspect-video overflow-hidden">
                            <?php the_post_thumbnail('rodust-featured', 'class=w-full h-full object-cover'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-3 text-gray-800 hover:text-blue-600">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="text-gray-600 mb-4">
                            <?php the_excerpt(); ?>
                        </div>
                        
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
        
        <!-- Paginação -->
        <div class="mt-8">
            <?php
            the_posts_pagination(array(
                'class' => 'flex justify-center space-x-2',
                'prev_text' => '← Anterior',
                'next_text' => 'Próxima →',
            ));
            ?>
        </div>
    <?php else : ?>
        <div class="text-center py-12">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Nenhum post encontrado</h2>
            <p class="text-gray-600">Parece que não há conteúdo disponível no momento.</p>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>