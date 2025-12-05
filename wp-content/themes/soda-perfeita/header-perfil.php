<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> style="background: #0b4395 url('<?php echo get_template_directory_uri(); ?>/imgs/back_desk.png') no-repeat right center; background-size: cover;">
    <?php wp_body_open(); ?>
    
    <div id="page" class="site">
        

        <main class="site-main">