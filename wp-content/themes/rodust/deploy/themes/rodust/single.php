<?php get_header(); ?>

<article class="container mx-auto px-4 py-12 md:py-16 max-w-4xl">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Cabeçalho do post -->
        <header class="mb-10 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4"><?php the_title(); ?></h1>
            
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <?php echo get_the_date(); ?>
                </span>
                
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Por <?php the_author(); ?>
                </span>
                
                <?php if (has_category()) : ?>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <?php the_category(', '); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>
        
        <!-- Imagem destacada -->
        <?php if (has_post_thumbnail()) : ?>
            <div class="mb-10 rounded-lg overflow-hidden shadow-lg">
                <?php the_post_thumbnail('large', 'class=w-full h-auto rounded-lg shadow-md'); ?>
            </div>
        <?php endif; ?>
        
        <!-- Conteúdo -->
        <div class="prose prose-lg lg:prose-xl max-w-none mb-12">
            <?php the_content(); ?>
        </div>
        
        <!-- Tags -->
        <?php if (has_tag()) : ?>
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-3">Tags:</h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $tags = get_the_tags();
                    foreach ($tags as $tag) {
                        echo '<span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200 transition-colors">';
                        echo '<a href="' . get_tag_link($tag->term_id) . '">#' . $tag->name . '</a>';
                        echo '</span>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Navegação entre posts -->
        <nav class="border-t border-gray-200 pt-8">
            <div class="flex justify-between">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <div class="flex-1 pr-4">
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo get_permalink($prev_post); ?>" class="group block">
                            <p class="text-sm text-gray-500 mb-1">← Post anterior</p>
                            <p class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                <?php echo get_the_title($prev_post); ?>
                            </p>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1 pl-4 text-right">
                    <?php if ($next_post) : ?>
                        <a href="<?php echo get_permalink($next_post); ?>" class="group block">
                            <p class="text-sm text-gray-500 mb-1">Próximo post →</p>
                            <p class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                <?php echo get_the_title($next_post); ?>
                            </p>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
    <?php endwhile; ?>
</article>

<!-- Comentários -->
<?php if (comments_open() || get_comments_number()) : ?>
    <section class="container mx-auto px-4 py-8 max-w-4xl border-t border-gray-200">
        <?php comments_template(); ?>
    </section>
<?php endif; ?>


<?php get_footer(); ?>