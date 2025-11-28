<?php get_header(); ?>

<main class="container mx-auto px-4 py-12 md:py-16">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <header class="mb-10 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4"><?php the_title(); ?></h1>
            </header>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="mb-10 rounded-lg overflow-hidden shadow-lg">
                    <?php the_post_thumbnail('large', 'class=w-full h-auto rounded-lg shadow-md'); ?>
                </div>
            <?php endif; ?>

            <!-- Adicionando as classes prose para estilizar o conteúdo -->
            <div class="entry-content prose prose-lg lg:prose-xl max-w-none">
                <?php the_content(); ?>
            </div>

        </article>
    <?php endwhile; ?>
</main>


<?php get_footer(); ?>
