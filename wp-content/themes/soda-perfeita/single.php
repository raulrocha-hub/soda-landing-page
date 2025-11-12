<?php get_header(); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card shadow-sm border-0'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="card-img-top">
                            <?php the_post_thumbnail('large', array('class' => 'img-fluid w-100')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <header class="mb-4">
                            <h1 class="card-title h2"><?php the_title(); ?></h1>
                            
                            <div class="text-muted small mb-3">
                                <span><?php echo get_the_date(); ?></span> • 
                                <span><?php the_author(); ?></span> •
                                <span><?php the_category(', '); ?></span>
                            </div>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>

                        <footer class="mt-4 pt-3 border-top">
                            <?php the_tags('<div class="tags"><span class="badge bg-primary me-1">', '</span><span class="badge bg-primary me-1">', '</span></div>'); ?>
                        </footer>
                    </div>
                </article>

                <!-- Navegação entre Posts -->
                <nav class="mt-4">
                    <div class="row">
                        <div class="col-6">
                            <?php previous_post_link('%link', '&laquo; Post Anterior'); ?>
                        </div>
                        <div class="col-6 text-end">
                            <?php next_post_link('%link', 'Próximo Post &raquo;'); ?>
                        </div>
                    </div>
                </nav>

                <!-- Comentários -->
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div class="mt-5">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>