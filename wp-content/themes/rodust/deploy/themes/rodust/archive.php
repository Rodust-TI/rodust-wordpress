<?php get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <header class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <?php
            if (is_category()) {
                single_cat_title('Categoria: ');
            } elseif (is_tag()) {
                single_tag_title('Tag: ');
            } elseif (is_author()) {
                echo 'Posts por: ' . get_the_author();
            } elseif (is_date()) {
                echo 'Arquivo: ' . get_the_date('F Y');
            } else {
                echo 'Arquivo';
            }
            ?>
        </h1>
        
        <?php if (is_category() && category_description()) : ?>
            <div class="text-gray-600 max-w-2xl mx-auto">
                <?php echo category_description(); ?>
            </div>
        <?php endif; ?>
    </header>
    
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="aspect-video overflow-hidden">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('rodust-featured', 'class=w-full h-full object-cover hover:scale-105 transition-transform'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-3 text-gray-800">
                            <a href="<?php the_permalink(); ?>" class="hover:text-blue-600 transition-colors">
                                <?php the_title(); ?>
                            </a>
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
        <div class="mt-12">
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
            <p class="text-gray-600 mb-6">Não há posts nesta categoria no momento.</p>
            <a href="<?php echo home_url(); ?>" class="btn-primary">
                Voltar ao início
            </a>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>