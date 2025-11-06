<?php get_header(); ?>

<section class="content-section py-5">
    <div class="container">
        <div class="row">
            <?php if (have_posts()): ?>
                <?php while (have_posts()): the_post(); ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <article id="post-<?php the_ID(); ?>" <?php post_class('card h-100'); ?>>
                            <?php if (has_post_thumbnail()): ?>
                                <div class="card-img-top">
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h3 class="card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="card-text">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Saiba Mais</a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                
                <div class="col-12">
                    <nav class="pagination-nav mt-4">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => 'Anterior',
                            'next_text' => 'Próximo',
                        ));
                        ?>
                    </nav>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <p>Nenhum conteúdo encontrado.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>