<?php get_header(); ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <header class="mb-4">
                <h1 class="h3">
                    <?php
                    printf(
                        esc_html__('Resultados para: "%s"', 'soda-perfeita'),
                        '<span class="text-primary">' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
            </header>

            <?php if (have_posts()) : ?>
                <div class="row">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="col-md-6 mb-4">
                            <article class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h2 class="card-title h5">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                    
                                    <div class="card-text small text-muted">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?php echo get_the_date(); ?>
                                        </small>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Paginação -->
                <div class="mt-4">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('&laquo; Anterior', 'soda-perfeita'),
                        'next_text' => __('Próxima &raquo;', 'soda-perfeita'),
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-search fa-2x mb-3 text-muted"></i>
                    <h3><?php esc_html_e('Nenhum resultado encontrado', 'soda-perfeita'); ?></h3>
                    <p class="mb-3"><?php esc_html_e('Tente novamente com palavras-chave diferentes.', 'soda-perfeita'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>