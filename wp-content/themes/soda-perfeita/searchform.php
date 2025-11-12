<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="input-group">
        <input type="search" class="form-control" placeholder="<?php esc_attr_e('Digite sua busca...', 'soda-perfeita'); ?>" value="<?php echo get_search_query(); ?>" name="s">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            <span class="visually-hidden"><?php esc_html_e('Buscar', 'soda-perfeita'); ?></span>
        </button>
    </div>
</form>