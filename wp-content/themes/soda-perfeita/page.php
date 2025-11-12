<?php 
/*
Template Name: Full-width layout
Template Post Type: post, page, event
*/
get_header(); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card shadow-sm border-0'); ?>>
                    <div class="card-body">
                        <header class="mb-4">
                            <h1 class="card-title h2"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>

                        <?php
                        // Paginação para páginas com <!--nextpage-->
                        wp_link_pages(array(
                            'before' => '<nav class="page-links mt-4"><span class="text-muted">Páginas:</span>',
                            'after' => '</nav>',
                            'link_before' => '<span class="page-number">',
                            'link_after' => '</span>',
                        ));
                        ?>
                    </div>
                </article>

                <!-- Comentários (se habilitados para páginas) -->
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div class="mt-5">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
              <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>