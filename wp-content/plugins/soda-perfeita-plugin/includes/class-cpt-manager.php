<?php
/**
 * Custom Post Type Manager for Soda Perfeita Plugin
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento de CPTs e campos ACF
 */
class SodaPerfeita_CPT_Manager {
    
    private $post_types;
    private $taxonomies;
    
    public function __construct() {
        $this->setup_post_types();
        $this->setup_taxonomies();

        add_action('init', array($this, 'register_custom_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('acf/init', array($this, 'register_acf_fields'));
    }

    public function init() {
        // Método mantido para compatibilidade
    }
    
    /**
     * Configura os Custom Post Types
     */
    private function setup_post_types() {
        $this->post_types = array(
            'sp_tarefas' => array(
                'labels' => array(
                    'name' => 'Tarefas Administrativas',
                    'singular_name' => 'Tarefa',
                    'menu_name' => 'Tarefas SP',
                    'all_items' => 'Todas as Tarefas',
                    'add_new' => 'Nova Tarefa',
                    'add_new_item' => 'Adicionar Nova Tarefa',
                    'edit_item' => 'Editar Tarefa',
                    'new_item' => 'Nova Tarefa',
                    'view_item' => 'Ver Tarefa',
                    'search_items' => 'Buscar Tarefas',
                    'not_found' => 'Nenhuma tarefa encontrada',
                    'not_found_in_trash' => 'Nenhuma tarefa na lixeira'
                ),
                'public' => false,
                'show_ui' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-clipboard',
                'supports' => array('title', 'editor'),
                'show_in_rest' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ),
            'sp_logs' => array(
                'labels' => array(
                    'name' => 'Logs do Sistema',
                    'singular_name' => 'Log',
                    'menu_name' => 'Logs SP',
                    'all_items' => 'Todos os Logs',
                    'add_new' => 'Adicionar Log',
                    'add_new_item' => 'Adicionar Novo Log',
                    'edit_item' => 'Editar Log',
                    'new_item' => 'Novo Log',
                    'view_item' => 'Ver Log',
                    'search_items' => 'Buscar Logs',
                    'not_found' => 'Nenhum log encontrado',
                    'not_found_in_trash' => 'Nenhum log na lixeira'
                ),
                'public' => false,
                'show_ui' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-media-text',
                'supports' => array('title', 'editor'),
                'show_in_rest' => false,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            )
        );
    }
    
    /**
     * Configura as taxonomias
     */
    private function setup_taxonomies() {
        $this->taxonomies = array(
            // Removidas taxonomias relacionadas aos post types excluídos
        );
    }
    
    /**
     * Registra os Custom Post Types
     */
    public function register_custom_post_types() {
        foreach ($this->post_types as $post_type => $args) {
            register_post_type($post_type, $args);
        }
    }
    
    /**
     * Registra as taxonomias
     */
    public function register_taxonomies() {
        foreach ($this->taxonomies as $taxonomy => $args) {
            $post_types = $args['post_types'];
            unset($args['post_types']);
            
            register_taxonomy($taxonomy, $post_types, $args);
        }
    }
    
    /**
     * Registra os campos ACF
     */
    public function register_acf_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        // Campos para Tarefas (mantido)
        acf_add_local_field_group(array(
            'key' => 'group_sp_tarefas',
            'title' => 'Detalhes da Tarefa',
            'fields' => $this->get_tarefa_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'sp_tarefas',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Campos para usuários (customers) do WooCommerce
        acf_add_local_field_group(array(
            'key' => 'group_sp_customer_fields',
            'title' => 'Informações Adicionais do Cliente',
            'fields' => $this->get_customer_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'user_role',
                        'operator' => '==',
                        'value' => 'all',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
    
    /**
     * Retorna campos ACF para Customers (usuários WooCommerce)
     */
    private function get_customer_fields() {
        return array(
            array(
                'key' => 'field_customer_tier',
                'label' => 'Tier Atual',
                'name' => 'tier_atual',
                'type' => 'select',
                'choices' => array(
                    'tier_1' => 'Tier 1 - Valor Base',
                    'tier_2' => 'Tier 2 - Performance',
                    'tier_3' => 'Tier 3 - Excelência',
                ),
                'default_value' => 'tier_1',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_status',
                'label' => 'Status do Cliente',
                'name' => 'status_cliente',
                'type' => 'select',
                'choices' => array(
                    'prospeccao' => 'Prospecção',
                    'aprovado' => 'Aprovado',
                    'contrato_assinado' => 'Contrato Assinado',
                    'ativo' => 'Ativo',
                    'inadimplente' => 'Inadimplente',
                    'bloqueado' => 'Bloqueado',
                    'cancelado' => 'Cancelado',
                ),
                'default_value' => 'prospeccao',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_treinamento_concluido',
                'label' => 'Treinamento Concluído',
                'name' => 'treinamento_concluido',
                'type' => 'true_false',
                'instructions' => 'Cliente concluiu o treinamento obrigatório',
                'default_value' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_pedidos_bloqueados',
                'label' => 'Pedidos Bloqueados',
                'name' => 'pedidos_bloqueados',
                'type' => 'true_false',
                'instructions' => 'Pedidos estão bloqueados para este cliente',
                'default_value' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_data_adesao',
                'label' => 'Data de Adesão',
                'name' => 'data_adesao',
                'type' => 'date_picker',
                'instructions' => 'Data em que o cliente aderiu ao programa',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_franqueado',
                'label' => 'Franqueado Responsável',
                'name' => 'franqueado_responsavel',
                'type' => 'post_object',
                'post_type' => array('franqueado'), // Seu CPT de franqueado criado via ACF
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_distribuidor',
                'label' => 'Distribuidor Responsável',
                'name' => 'distribuidor_responsavel',
                'type' => 'post_object',
                'post_type' => array('distribuidor'), // Seu CPT de distribuidor criado via ACF
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_customer_estabelecimento',
                'label' => 'Estabelecimento Vinculado',
                'name' => 'estabelecimento_vinculado',
                'type' => 'post_object',
                'post_type' => array('estabelecimento'), // Seu CPT de estabelecimento criado via ACF
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
        );
    }
    
    /**
     * Retorna campos ACF para Tarefas (mantido)
     */
    private function get_tarefa_fields() {
        return array(
            array(
                'key' => 'field_tarefa_prioridade',
                'label' => 'Prioridade',
                'name' => 'prioridade',
                'type' => 'select',
                'choices' => array(
                    'baixa' => 'Baixa',
                    'media' => 'Média',
                    'alta' => 'Alta',
                    'urgente' => 'Urgente',
                ),
                'default_value' => 'media',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_tarefa_status',
                'label' => 'Status',
                'name' => 'status',
                'type' => 'select',
                'choices' => array(
                    'pendente' => 'Pendente',
                    'em_andamento' => 'Em Andamento',
                    'concluida' => 'Concluída',
                    'cancelada' => 'Cancelada',
                ),
                'default_value' => 'pendente',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_tarefa_cliente',
                'label' => 'Cliente Relacionado',
                'name' => 'cliente_id',
                'type' => 'user', // Agora usa user em vez de post_object para sp_clientes
                'role' => 'customer', // Papel de cliente do WooCommerce
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_tarefa_responsavel',
                'label' => 'Responsável',
                'name' => 'responsavel',
                'type' => 'user',
                'instructions' => 'Usuário responsável pela tarefa',
                'role' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_tarefa_data_limite',
                'label' => 'Data Limite',
                'name' => 'data_limite',
                'type' => 'date_picker',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_tarefa_data_conclusao',
                'label' => 'Data de Conclusão',
                'name' => 'data_conclusao',
                'type' => 'date_picker',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
        );
    }
    
    /**
     * Retorna a lista de post types registrados
     */
    public function get_registered_post_types() {
        return $this->post_types;
    }
    
    /**
     * Retorna a lista de taxonomias registradas
     */
    public function get_registered_taxonomies() {
        return $this->taxonomies;
    }
}