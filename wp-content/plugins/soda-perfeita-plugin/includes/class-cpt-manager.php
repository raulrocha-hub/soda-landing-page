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
        add_filter('manage_sp_clientes_posts_columns', array($this, 'add_clientes_columns'));
        add_action('manage_sp_clientes_posts_custom_column', array($this, 'manage_clientes_columns'), 10, 2);
        add_filter('manage_edit-sp_clientes_sortable_columns', array($this, 'clientes_sortable_columns'));
    }

    public function init() {
        // Método mantido para compatibilidade
    }
    
    /**
     * Configura os Custom Post Types
     */
    private function setup_post_types() {
        $this->post_types = array(
            'sp_clientes' => array(
                'labels' => array(
                    'name' => 'Clientes Soda Perfeita',
                    'singular_name' => 'Cliente',
                    'menu_name' => 'Clientes SP',
                    'all_items' => 'Todos os Clientes',
                    'add_new' => 'Adicionar Novo',
                    'add_new_item' => 'Adicionar Novo Cliente',
                    'edit_item' => 'Editar Cliente',
                    'new_item' => 'Novo Cliente',
                    'view_item' => 'Ver Cliente',
                    'search_items' => 'Buscar Clientes',
                    'not_found' => 'Nenhum cliente encontrado',
                    'not_found_in_trash' => 'Nenhum cliente na lixeira'
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => 'dashicons-businessperson',
                'supports' => array('title', 'thumbnail'),
                'show_in_rest' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'rewrite' => array('slug' => 'clientes-sp'),
            ),
            'sp_pedidos' => array(
                'labels' => array(
                    'name' => 'Pedidos de Xarope',
                    'singular_name' => 'Pedido',
                    'menu_name' => 'Pedidos SP',
                    'all_items' => 'Todos os Pedidos',
                    'add_new' => 'Novo Pedido',
                    'add_new_item' => 'Adicionar Novo Pedido',
                    'edit_item' => 'Editar Pedido',
                    'new_item' => 'Novo Pedido',
                    'view_item' => 'Ver Pedido',
                    'search_items' => 'Buscar Pedidos',
                    'not_found' => 'Nenhum pedido encontrado',
                    'not_found_in_trash' => 'Nenhum pedido na lixeira'
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => 'dashicons-cart',
                'supports' => array('title'),
                'show_in_rest' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ),
            'sp_distribuidores' => array(
                'labels' => array(
                    'name' => 'Distribuidores DVG',
                    'singular_name' => 'Distribuidor',
                    'menu_name' => 'Distribuidores',
                    'all_items' => 'Todos os Distribuidores',
                    'add_new' => 'Adicionar Distribuidor',
                    'add_new_item' => 'Adicionar Novo Distribuidor',
                    'edit_item' => 'Editar Distribuidor',
                    'new_item' => 'Novo Distribuidor',
                    'view_item' => 'Ver Distribuidor',
                    'search_items' => 'Buscar Distribuidores',
                    'not_found' => 'Nenhum distribuidor encontrado',
                    'not_found_in_trash' => 'Nenhum distribuidor na lixeira'
                ),
                'public' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-store',
                'supports' => array('title', 'thumbnail'),
                'show_in_rest' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ),
            'sp_franqueados' => array(
                'labels' => array(
                    'name' => 'Franqueados Preshh',
                    'singular_name' => 'Franqueado',
                    'menu_name' => 'Franqueados',
                    'all_items' => 'Todos os Franqueados',
                    'add_new' => 'Adicionar Franqueado',
                    'add_new_item' => 'Adicionar Novo Franqueado',
                    'edit_item' => 'Editar Franqueado',
                    'new_item' => 'Novo Franqueado',
                    'view_item' => 'Ver Franqueado',
                    'search_items' => 'Buscar Franqueados',
                    'not_found' => 'Nenhum franqueado encontrado',
                    'not_found_in_trash' => 'Nenhum franqueado na lixeira'
                ),
                'public' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-groups',
                'supports' => array('title', 'thumbnail'),
                'show_in_rest' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ),
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
            'sp_regiao' => array(
                'post_types' => array('sp_clientes', 'sp_distribuidores', 'sp_franqueados'),
                'labels' => array(
                    'name' => 'Regiões',
                    'singular_name' => 'Região',
                    'search_items' => 'Buscar Regiões',
                    'all_items' => 'Todas as Regiões',
                    'parent_item' => 'Região Pai',
                    'parent_item_colon' => 'Região Pai:',
                    'edit_item' => 'Editar Região',
                    'update_item' => 'Atualizar Região',
                    'add_new_item' => 'Adicionar Nova Região',
                    'new_item_name' => 'Nome da Nova Região',
                    'menu_name' => 'Regiões'
                ),
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'rewrite' => array('slug' => 'regiao'),
            ),
            'sp_status_pedido' => array(
                'post_types' => array('sp_pedidos'),
                'labels' => array(
                    'name' => 'Status de Pedido',
                    'singular_name' => 'Status',
                    'search_items' => 'Buscar Status',
                    'all_items' => 'Todos os Status',
                    'edit_item' => 'Editar Status',
                    'update_item' => 'Atualizar Status',
                    'add_new_item' => 'Adicionar Novo Status',
                    'new_item_name' => 'Nome do Novo Status',
                    'menu_name' => 'Status Pedido'
                ),
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
            )
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
        
        // Campos para Clientes
        acf_add_local_field_group(array(
            'key' => 'group_sp_clientes',
            'title' => 'Informações do Cliente',
            'fields' => $this->get_cliente_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'sp_clientes',
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
        
        // Campos para Pedidos
        acf_add_local_field_group(array(
            'key' => 'group_sp_pedidos',
            'title' => 'Detalhes do Pedido',
            'fields' => $this->get_pedido_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'sp_pedidos',
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
        
        // Campos para Distribuidores
        acf_add_local_field_group(array(
            'key' => 'group_sp_distribuidores',
            'title' => 'Informações do Distribuidor',
            'fields' => $this->get_distribuidor_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'sp_distribuidores',
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
        
        // Campos para Franqueados
        acf_add_local_field_group(array(
            'key' => 'group_sp_franqueados',
            'title' => 'Informações do Franqueado',
            'fields' => $this->get_franqueado_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'sp_franqueados',
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
        
        // Campos para Tarefas
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
    }
    
    /**
     * Retorna campos ACF para Clientes
     */
    private function get_cliente_fields() {
        return array(
            array(
                'key' => 'field_cliente_cnpj',
                'label' => 'CNPJ',
                'name' => 'cnpj',
                'type' => 'text',
                'instructions' => 'CNPJ do estabelecimento',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
                'instructions' => 'Email de contato principal',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_telefone',
                'label' => 'Telefone',
                'name' => 'telefone',
                'type' => 'text',
                'instructions' => 'Telefone para contato',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_endereco',
                'label' => 'Endereço Completo',
                'name' => 'endereco',
                'type' => 'textarea',
                'instructions' => 'Endereço completo para entrega',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_status',
                'label' => 'Status do Cliente',
                'name' => 'status',
                'type' => 'select',
                'instructions' => 'Status atual do cliente no sistema',
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
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_cliente_status_financeiro',
                'label' => 'Status Financeiro',
                'name' => 'status_financeiro',
                'type' => 'select',
                'choices' => array(
                    'adimplente' => 'Adimplente',
                    'inadimplente' => 'Inadimplente',
                ),
                'default_value' => 'adimplente',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_cliente_tier',
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
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_cliente_franqueado',
                'label' => 'Franqueado Responsável',
                'name' => 'franqueado_responsavel',
                'type' => 'post_object',
                'post_type' => array('sp_franqueados'),
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_distribuidor',
                'label' => 'Distribuidor Responsável',
                'name' => 'distribuidor_responsavel',
                'type' => 'post_object',
                'post_type' => array('sp_distribuidores'),
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_data_adesao',
                'label' => 'Data de Adesão',
                'name' => 'data_adesao',
                'type' => 'date_picker',
                'instructions' => 'Data em que o cliente aderiu ao programa',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_treinamento_concluido',
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
                'key' => 'field_cliente_pedidos_bloqueados',
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
                'key' => 'field_cliente_ultimo_pedido',
                'label' => 'Data do Último Pedido',
                'name' => 'data_ultimo_pedido',
                'type' => 'date_picker',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_cliente_beneficios',
                'label' => 'Benefícios Ativos',
                'name' => 'beneficios_ativos',
                'type' => 'repeater',
                'instructions' => 'Benefícios ativos para este cliente',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_beneficio_nome',
                        'label' => 'Benefício',
                        'name' => 'beneficio',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_beneficio_status',
                        'label' => 'Status',
                        'name' => 'status',
                        'type' => 'select',
                        'choices' => array(
                            'ativo' => 'Ativo',
                            'pendente' => 'Pendente',
                            'expirado' => 'Expirado',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_cliente_usuario_vinculado',
                'label' => 'Usuário Vinculado',
                'name' => 'usuario_vinculado',
                'type' => 'user',
                'instructions' => 'Usuário WordPress vinculado a este cliente',
                'role' => array('cliente_final'),
                'allow_null' => 1,
                'multiple' => 0,
            ),
        );
    }
    
    /**
     * Retorna campos ACF para Pedidos
     */
    private function get_pedido_fields() {
        return array(
            array(
                'key' => 'field_pedido_cliente',
                'label' => 'Cliente',
                'name' => 'cliente_id',
                'type' => 'post_object',
                'post_type' => array('sp_clientes'),
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'required' => 1,
            ),
            array(
                'key' => 'field_pedido_distribuidor',
                'label' => 'Distribuidor',
                'name' => 'distribuidor_id',
                'type' => 'post_object',
                'post_type' => array('sp_distribuidores'),
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
                'required' => 1,
            ),
            array(
                'key' => 'field_pedido_quantidade',
                'label' => 'Quantidade de Garrafas',
                'name' => 'quantidade_garrafas',
                'type' => 'number',
                'instructions' => 'Número de garrafas de xarope solicitadas',
                'required' => 1,
                'min' => 1,
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_pedido_valor_unitario',
                'label' => 'Valor Unitário',
                'name' => 'valor_unitario',
                'type' => 'number',
                'instructions' => 'Valor unitário do xarope (R$)',
                'required' => 1,
                'min' => 0,
                'step' => '0.01',
                'default_value' => 45.00,
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_pedido_valor_total',
                'label' => 'Valor Total',
                'name' => 'valor_total',
                'type' => 'number',
                'instructions' => 'Valor total do pedido (calculado automaticamente)',
                'readonly' => 1,
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_pedido_status',
                'label' => 'Status do Pedido',
                'name' => 'status',
                'type' => 'select',
                'choices' => array(
                    'solicitado' => 'Solicitado',
                    'aprovado' => 'Aprovado',
                    'faturado' => 'Faturado',
                    'enviado' => 'Enviado',
                    'entregue' => 'Entregue',
                    'cancelado' => 'Cancelado',
                ),
                'default_value' => 'solicitado',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_pedido_data',
                'label' => 'Data do Pedido',
                'name' => 'data_pedido',
                'type' => 'date_time_picker',
                'instructions' => 'Data e hora da solicitação do pedido',
                'required' => 1,
                'display_format' => 'd/m/Y H:i',
                'return_format' => 'Y-m-d H:i:s',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_pedido_data_entrega',
                'label' => 'Data de Entrega',
                'name' => 'data_entrega',
                'type' => 'date_time_picker',
                'instructions' => 'Data e hora da entrega do pedido',
                'display_format' => 'd/m/Y H:i',
                'return_format' => 'Y-m-d H:i:s',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_pedido_observacoes',
                'label' => 'Observações',
                'name' => 'observacoes',
                'type' => 'textarea',
                'instructions' => 'Observações adicionais sobre o pedido',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
        );
    }
    
    /**
     * Retorna campos ACF para Distribuidores
     */
    private function get_distribuidor_fields() {
        return array(
            array(
                'key' => 'field_distribuidor_contato',
                'label' => 'Nome do Contato',
                'name' => 'contato',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_distribuidor_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
                'required' => 1,
            ),
            array(
                'key' => 'field_distribuidor_telefone',
                'label' => 'Telefone',
                'name' => 'telefone',
                'type' => 'text',
            ),
            array(
                'key' => 'field_distribuidor_endereco',
                'label' => 'Endereço',
                'name' => 'endereco',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_distribuidor_regiao',
                'label' => 'Região de Atuação',
                'name' => 'regiao',
                'type' => 'text',
                'instructions' => 'Região/Estado de atuação do distribuidor',
            ),
            array(
                'key' => 'field_distribuidor_status',
                'label' => 'Status',
                'name' => 'status',
                'type' => 'select',
                'choices' => array(
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                    'suspenso' => 'Suspenso',
                ),
                'default_value' => 'ativo',
            ),
        );
    }
    
    /**
     * Retorna campos ACF para Franqueados
     */
    private function get_franqueado_fields() {
        return array(
            array(
                'key' => 'field_franqueado_contato',
                'label' => 'Nome do Franqueado',
                'name' => 'contato',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_franqueado_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
                'required' => 1,
            ),
            array(
                'key' => 'field_franqueado_telefone',
                'label' => 'Telefone',
                'name' => 'telefone',
                'type' => 'text',
            ),
            array(
                'key' => 'field_franqueado_regiao',
                'label' => 'Região de Atuação',
                'name' => 'regiao',
                'type' => 'text',
                'instructions' => 'Região/Estado de atuação do franqueado',
            ),
            array(
                'key' => 'field_franqueado_comissao',
                'label' => 'Percentual de Comissão',
                'name' => 'percentual_comissao',
                'type' => 'number',
                'instructions' => 'Percentual de comissão sobre as locações (%)',
                'min' => 0,
                'max' => 100,
                'step' => '0.01',
            ),
            array(
                'key' => 'field_franqueado_status',
                'label' => 'Status',
                'name' => 'status',
                'type' => 'select',
                'choices' => array(
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                ),
                'default_value' => 'ativo',
            ),
        );
    }
    
    /**
     * Retorna campos ACF para Tarefas
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
                'type' => 'post_object',
                'post_type' => array('sp_clientes'),
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
     * Adiciona colunas personalizadas na listagem de Clientes
     */
    public function add_clientes_columns($columns) {
        $new_columns = array(
            'cb' => $columns['cb'],
            'title' => $columns['title'],
            'cnpj' => 'CNPJ',
            'status' => 'Status',
            'tier' => 'Tier',
            'franqueado' => 'Franqueado',
            'distribuidor' => 'Distribuidor',
            'data_adesao' => 'Data Adesão',
            'ultimo_pedido' => 'Último Pedido',
            'date' => $columns['date'],
        );
        
        return $new_columns;
    }
    
    /**
     * Gerencia o conteúdo das colunas personalizadas
     */
    public function manage_clientes_columns($column, $post_id) {
        switch ($column) {
            case 'cnpj':
                echo get_field('cnpj', $post_id);
                break;
                
            case 'status':
                $status = get_field('status', $post_id);
                $status_labels = array(
                    'prospeccao' => 'Prospecção',
                    'aprovado' => 'Aprovado',
                    'contrato_assinado' => 'Contrato Assinado',
                    'ativo' => 'Ativo',
                    'inadimplente' => 'Inadimplente',
                    'bloqueado' => 'Bloqueado',
                    'cancelado' => 'Cancelado',
                );
                echo '<span class="status-badge status-' . esc_attr($status) . '">' . esc_html($status_labels[$status] ?? $status) . '</span>';
                break;
                
            case 'tier':
                $tier = get_field('tier_atual', $post_id);
                $tier_labels = array(
                    'tier_1' => 'Tier 1',
                    'tier_2' => 'Tier 2',
                    'tier_3' => 'Tier 3',
                );
                echo '<span class="tier-badge tier-' . esc_attr($tier) . '">' . esc_html($tier_labels[$tier] ?? $tier) . '</span>';
                break;
                
            case 'franqueado':
                $franqueado_id = get_field('franqueado_responsavel', $post_id);
                if ($franqueado_id) {
                    echo get_the_title($franqueado_id);
                } else {
                    echo '<span class="dashicons dashicons-minus"></span>';
                }
                break;
                
            case 'distribuidor':
                $distribuidor_id = get_field('distribuidor_responsavel', $post_id);
                if ($distribuidor_id) {
                    echo get_the_title($distribuidor_id);
                } else {
                    echo '<span class="dashicons dashicons-minus"></span>';
                }
                break;
                
            case 'data_adesao':
                $data_adesao = get_field('data_adesao', $post_id);
                if ($data_adesao) {
                    echo date('d/m/Y', strtotime($data_adesao));
                } else {
                    echo '<span class="dashicons dashicons-minus"></span>';
                }
                break;
                
            case 'ultimo_pedido':
                $ultimo_pedido = get_field('data_ultimo_pedido', $post_id);
                if ($ultimo_pedido) {
                    echo date('d/m/Y', strtotime($ultimo_pedido));
                } else {
                    echo '<span class="dashicons dashicons-minus"></span>';
                }
                break;
        }
    }
    
    /**
     * Define colunas ordenáveis
     */
    public function clientes_sortable_columns($columns) {
        $columns['data_adesao'] = 'data_adesao';
        $columns['ultimo_pedido'] = 'ultimo_pedido';
        return $columns;
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
    
    /**
     * Método para compatibilidade com CPT UI
     * Exporta configurações para importação no CPT UI
     */
    public function get_cptui_export_data() {
        $cptui_data = array();
        
        foreach ($this->post_types as $post_type => $args) {
            $cptui_data[$post_type] = array(
                'name' => $post_type,
                'label' => $args['labels']['name'],
                'singular_label' => $args['labels']['singular_name'],
                'description' => $args['description'] ?? '',
                'public' => $args['public'] ? 'true' : 'false',
                'publicly_queryable' => $args['publicly_queryable'] ?? 'true',
                'show_ui' => $args['show_ui'] ?? 'true',
                'show_in_nav_menus' => $args['show_in_nav_menus'] ?? 'true',
                'delete_with_user' => 'false',
                'show_in_rest' => $args['show_in_rest'] ? 'true' : 'false',
                'rest_base' => $post_type,
                'rest_controller_class' => 'WP_REST_Posts_Controller',
                'has_archive' => $args['has_archive'] ? 'true' : 'false',
                'has_archive_string' => $args['has_archive'] === true ? $post_type : $args['has_archive'],
                'exclude_from_search' => 'false',
                'capability_type' => $args['capability_type'] ?? 'post',
                'hierarchical' => $args['hierarchical'] ? 'true' : 'false',
                'rewrite' => $args['rewrite'] ? 'true' : 'false',
                'rewrite_slug' => $args['rewrite']['slug'] ?? $post_type,
                'rewrite_withfront' => 'true',
                'query_var' => 'true',
                'query_var_slug' => '',
                'menu_position' => $args['menu_position'] ?? '',
                'show_in_menu' => 'true',
                'show_in_menu_string' => '',
                'menu_icon' => $args['menu_icon'] ?? 'dashicons-admin-post',
                'supports' => $args['supports'] ?? array('title', 'editor'),
                'taxonomies' => isset($this->taxonomies[$post_type]) ? array($this->taxonomies[$post_type]) : array(),
            );
        }
        
        return $cptui_data;
    }
}