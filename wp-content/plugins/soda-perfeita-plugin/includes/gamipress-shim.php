<?php
// includes/gamipress-shim.php
if ( ! defined('ABSPATH') ) exit;

/**
 * Shim DEV para evitar Fatal Errors quando o GamiPress não está ativo.
 * Não implementa lógica real — apenas guarda o que foi "registrado".
 */
global $soda_gp_registry;
if ( ! is_array($soda_gp_registry) ) {
    $soda_gp_registry = array(
        'points_types'       => array(),
        'achievement_types'  => array(),
        'rank_types'         => array(),
    );
}

/* ---------- POINTS TYPES ---------- */

// Se não existir nem a real, define uma fake (no-op)
if ( ! function_exists('gamipress_add_points_type') ) {
    function gamipress_add_points_type( $args = array() ) {
        global $soda_gp_registry;
        $defaults = array(
            'name'           => '',
            'singular_name'  => '',
            'plural_name'    => '',
            'before_amount'  => '',
            'after_amount'   => '',
            'position'       => 'after',
        );
        $type = wp_parse_args( $args, $defaults );
        if ( empty($type['name']) ) return null;
        $soda_gp_registry['points_types'][ $type['name'] ] = $type;
        return $type['name'];
    }
}

// Alias: se existir a "register" real mas não existir a "add", cria um proxy
if ( ! function_exists('gamipress_add_points_type') && function_exists('gamipress_register_points_type') ) {
    function gamipress_add_points_type( $args = array() ) {
        return gamipress_register_points_type( $args );
    }
}

/* ---------- ACHIEVEMENT TYPES ---------- */

if ( ! function_exists('gamipress_register_achievement_type') ) {
    function gamipress_register_achievement_type( $type = '', $args = array() ) {
        global $soda_gp_registry;
        if ( empty($type) ) return null;
        $soda_gp_registry['achievement_types'][ $type ] = $args;
        return $type;
    }
}

// Substitua apenas a função gamipress_add_achievement_type no shim:
if ( ! function_exists('gamipress_add_achievement_type') ) {
    function gamipress_add_achievement_type( $type = '', $args = array() ) {
        // Se o GamiPress real existe
        if ( function_exists('gamipress_register_achievement_type') ) {
            // Verifica se é a estrutura nova ou antiga
            if ( isset($args['singular_name']) && isset($args['plural_name']) ) {
                // Estrutura nova - passa diretamente
                return gamipress_register_achievement_type( $type, $args );
            } else {
                // Estrutura antiga - converte
                $converted_args = array(
                    'singular_name' => $args['singular_name'] ?? '',
                    'plural_name'   => $args['plural_name'] ?? '',
                    'slug'          => $args['slug'] ?? '',
                    'supports'      => $args['supports'] ?? array(),
                    'show_in_menu'  => $args['show_in_menu'] ?? true,
                );
                return gamipress_register_achievement_type( $type, $converted_args );
            }
        }
        
        // Fallback para shim
        global $soda_gp_registry;
        if ( empty($type) ) return null;
        $soda_gp_registry['achievement_types'][ $type ] = $args;
        return $type;
    }
}

/* ---------- RANK TYPES ---------- */

if ( ! function_exists('gamipress_register_rank_type') ) {
    function gamipress_register_rank_type( $type = '', $args = array() ) {
        global $soda_gp_registry;
        if ( empty($type) ) return null;
        $soda_gp_registry['rank_types'][ $type ] = $args;
        return $type;
    }
}

if ( ! function_exists('gamipress_add_rank_type') ) {
    function gamipress_add_rank_type( $type = '', $args = array() ) {
        // Proxy para register se existir; senão, usa o shim
        if ( function_exists('gamipress_register_rank_type') ) {
            return gamipress_register_rank_type( $type, $args );
        }
        global $soda_gp_registry;
        if ( empty($type) ) return null;
        $soda_gp_registry['rank_types'][ $type ] = $args;
        return $type;
    }
}

/* ---------- Helpers de debug (opcionais) ---------- */
if ( ! function_exists('soda_gp_get_points_types') ) {
    function soda_gp_get_points_types() {
        global $soda_gp_registry;
        return isset($soda_gp_registry['points_types']) ? $soda_gp_registry['points_types'] : array();
    }
}
if ( ! function_exists('soda_gp_get_achievement_types') ) {
    function soda_gp_get_achievement_types() {
        global $soda_gp_registry;
        return isset($soda_gp_registry['achievement_types']) ? $soda_gp_registry['achievement_types'] : array();
    }
}
if ( ! function_exists('soda_gp_get_rank_types') ) {
    function soda_gp_get_rank_types() {
        global $soda_gp_registry;
        return isset($soda_gp_registry['rank_types']) ? $soda_gp_registry['rank_types'] : array();
    }
}
