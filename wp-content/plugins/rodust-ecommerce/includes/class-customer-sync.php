<?php
/**
 * Customer Sync - Sincronização de dados de clientes com Laravel
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Customer_Sync {
    
    public function __construct() {
        // Hook quando usuário atualiza perfil
        add_action('profile_update', [$this, 'sync_customer_on_update'], 10, 2);
        
        // Hook quando usuário é criado
        add_action('user_register', [$this, 'sync_customer_on_create'], 10, 1);
        
        // AJAX para atualizar taxpayer_type no checkout
        add_action('wp_ajax_rodust_update_taxpayer_type', [$this, 'ajax_update_taxpayer_type']);
        add_action('wp_ajax_nopriv_rodust_update_taxpayer_type', [$this, 'ajax_update_taxpayer_type']);
    }

    /**
     * Sincronizar cliente quando perfil é atualizado
     */
    public function sync_customer_on_update($user_id, $old_user_data) {
        error_log("=== RODUST CUSTOMER UPDATE HOOK ===");
        error_log("User ID: {$user_id}");
        
        try {
            $user = get_userdata($user_id);
            if (!$user) {
                error_log("User not found: {$user_id}");
                return;
            }

            // Preparar dados do cliente
            $customer_data = $this->prepare_customer_data($user);
            
            error_log("Customer data prepared: " . print_r($customer_data, true));

            // Enviar para Laravel
            $api_client = new Rodust_API_Client();
            
            // Primeiro tentar buscar token do usuário
            $token = get_user_meta($user_id, 'rodust_api_token', true);
            
            if ($token) {
                // Usuário já tem token, atualizar via PUT /api/customers/me
                error_log("Updating customer with token...");
                $response = $api_client->put('/customers/me', $customer_data, [
                    'Authorization' => 'Bearer ' . $token
                ]);
            } else {
                // Usuário não tem token, usar endpoint de sincronização
                error_log("Syncing customer without token...");
                $response = $api_client->post('/customers/sync-from-wordpress', [
                    'customers' => [$customer_data]
                ]);
            }

            error_log("Laravel response: " . print_r($response, true));

            if ($response['success']) {
                error_log("Customer synced successfully to Laravel");
            } else {
                error_log("Failed to sync customer: " . ($response['error'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            error_log("ERROR in sync_customer_on_update: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Sincronizar cliente quando é criado
     */
    public function sync_customer_on_create($user_id) {
        error_log("=== RODUST CUSTOMER CREATE HOOK ===");
        error_log("User ID: {$user_id}");
        
        // Aguardar 2 segundos para garantir que todos os meta fields foram salvos
        sleep(2);
        
        // Chamar mesma função de update
        $user = get_userdata($user_id);
        $this->sync_customer_on_update($user_id, $user);
    }

    /**
     * Preparar dados do cliente para envio ao Laravel
     */
    private function prepare_customer_data($user) {
        return [
            'name' => $user->display_name ?: $user->user_login,
            'email' => $user->user_email,
            'phone' => get_user_meta($user->ID, 'billing_phone', true) ?: get_user_meta($user->ID, 'phone', true),
            'cpf' => get_user_meta($user->ID, 'billing_cpf', true) ?: get_user_meta($user->ID, 'cpf', true),
            'cnpj' => get_user_meta($user->ID, 'billing_cnpj', true) ?: get_user_meta($user->ID, 'cnpj', true),
            'person_type' => get_user_meta($user->ID, 'person_type', true) ?: 'F',
            'birth_date' => get_user_meta($user->ID, 'birth_date', true),
            'fantasy_name' => get_user_meta($user->ID, 'fantasy_name', true),
            'state_registration' => get_user_meta($user->ID, 'state_registration', true),
            'state_uf' => get_user_meta($user->ID, 'state_uf', true),
            'nfe_email' => get_user_meta($user->ID, 'nfe_email', true),
            'phone_commercial' => get_user_meta($user->ID, 'phone_commercial', true),
            'taxpayer_type' => get_user_meta($user->ID, 'taxpayer_type', true) ?: 9,
        ];
    }

    /**
     * AJAX: Atualizar taxpayer_type no checkout
     */
    public function ajax_update_taxpayer_type() {
        check_ajax_referer('rodust_checkout', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Usuário não autenticado']);
            return;
        }

        $user_id = get_current_user_id();
        $taxpayer_type = isset($_POST['taxpayer_type']) ? intval($_POST['taxpayer_type']) : 9;
        
        // Validar valor
        if (!in_array($taxpayer_type, [1, 2, 9])) {
            wp_send_json_error(['message' => 'Tipo de contribuinte inválido']);
            return;
        }

        error_log("=== UPDATE TAXPAYER TYPE ===");
        error_log("User ID: {$user_id}, Taxpayer Type: {$taxpayer_type}");

        try {
            // Buscar token
            $token = get_user_meta($user_id, 'rodust_api_token', true);
            
            if (!$token) {
                wp_send_json_error(['message' => 'Token não encontrado. Faça login novamente.']);
                return;
            }

            // Atualizar via API Laravel
            $api_client = new Rodust_API_Client();
            $response = $api_client->put('/customers/me', [
                'taxpayer_type' => $taxpayer_type
            ], [
                'Authorization' => 'Bearer ' . $token
            ]);

            error_log("Laravel response: " . print_r($response, true));

            if ($response['success']) {
                // Atualizar cache local
                update_user_meta($user_id, 'taxpayer_type', $taxpayer_type);
                
                wp_send_json_success([
                    'message' => 'Tipo de contribuinte atualizado com sucesso',
                    'taxpayer_type' => $taxpayer_type
                ]);
            } else {
                wp_send_json_error([
                    'message' => $response['error'] ?? 'Erro ao atualizar'
                ]);
            }

        } catch (Exception $e) {
            error_log("ERROR in ajax_update_taxpayer_type: " . $e->getMessage());
            wp_send_json_error(['message' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }
}

// Initialize
new Rodust_Customer_Sync();
