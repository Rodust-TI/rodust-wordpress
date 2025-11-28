<?php
/**
 * Template Name: Checkout
 */

get_header();
?>

<main class="container mx-auto px-4 py-8">
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php get_footer(); ?>
