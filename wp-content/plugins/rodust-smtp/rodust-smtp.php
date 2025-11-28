<?php
/**
 * Plugin Name: Rodust SMTP
 * Plugin URI: https://rodust.com.br
 * Description: Configuração SMTP para envio de e-mails do site Rodust
 * Version: 1.0.0
 * Author: Rodust
 * Text Domain: rodust-smtp
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class Rodust_SMTP {
    
    public function __construct() {
        // Carrega configurações automáticas
        $this->load_config();
        
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('phpmailer_init', array($this, 'configure_smtp'));
        
        // Auto-configuração na ativação
        register_activation_hook(__FILE__, array($this, 'auto_configure'));
    }
    
    /**
     * Carrega arquivo de configuração se existir
     */
    private function load_config() {
        $config_file = plugin_dir_path(__FILE__) . 'config.php';
        if (file_exists($config_file)) {
            include_once $config_file;
        }
    }
    
    /**
     * Configuração automática na ativação do plugin
     */
    public function auto_configure() {
        if (defined('RODUST_SMTP_HOST')) {
            $options = array(
                'smtp_host' => RODUST_SMTP_HOST,
                'smtp_port' => RODUST_SMTP_PORT,
                'smtp_secure' => RODUST_SMTP_SECURE,
                'smtp_username' => RODUST_SMTP_USERNAME,
                'smtp_password' => RODUST_SMTP_PASSWORD,
                'from_email' => RODUST_SMTP_FROM_EMAIL,
                'from_name' => RODUST_SMTP_FROM_NAME
            );
            
            update_option('rodust_smtp_options', $options);
        }
    }
    
    public function init() {
        // Carrega textdomain se necessário
        load_plugin_textdomain('rodust-smtp', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Adiciona página de configuração no admin
     */
    public function admin_menu() {
        add_options_page(
            'Configurações SMTP',
            'SMTP Rodust',
            'manage_options',
            'rodust-smtp',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Registra configurações
     */
    public function admin_init() {
        register_setting('rodust_smtp_settings', 'rodust_smtp_options');
        
        add_settings_section(
            'rodust_smtp_main',
            'Configurações SMTP',
            array($this, 'section_callback'),
            'rodust-smtp'
        );
        
        // Campos de configuração
        $fields = array(
            'smtp_host' => 'Servidor SMTP',
            'smtp_port' => 'Porta',
            'smtp_secure' => 'Segurança',
            'smtp_username' => 'Usuário (E-mail)',
            'smtp_password' => 'Senha',
            'from_email' => 'E-mail Remetente',
            'from_name' => 'Nome Remetente'
        );
        
        foreach ($fields as $field => $label) {
            add_settings_field(
                $field,
                $label,
                array($this, 'field_callback'),
                'rodust-smtp',
                'rodust_smtp_main',
                array('field' => $field, 'label' => $label)
            );
        }
    }
    
    public function section_callback() {
        echo '<p>Configure as credenciais SMTP para envio de e-mails.</p>';
        
        // Mostra presets comuns
        echo '<div class="notice notice-info inline" style="margin: 15px 0; padding: 10px;">
            <h4>Configurações Pré-definidas:</h4>
            <p><strong>Hostinger:</strong> smtp.hostinger.com | Porta: 587 | TLS</p>
            <p><strong>Gmail:</strong> smtp.gmail.com | Porta: 587 | TLS</p>
        </div>';
    }
    
    public function field_callback($args) {
        $options = get_option('rodust_smtp_options', array());
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        
        switch ($field) {
            case 'smtp_secure':
                echo '<select name="rodust_smtp_options[' . $field . ']" class="regular-text">
                    <option value="tls"' . selected($value, 'tls', false) . '>TLS</option>
                    <option value="ssl"' . selected($value, 'ssl', false) . '>SSL</option>
                </select>';
                break;
            case 'smtp_password':
                echo '<input type="password" name="rodust_smtp_options[' . $field . ']" value="' . esc_attr($value) . '" class="regular-text" autocomplete="new-password" />';
                break;
            case 'smtp_port':
                echo '<input type="number" name="rodust_smtp_options[' . $field . ']" value="' . esc_attr($value ?: '587') . '" class="small-text" />';
                break;
            default:
                echo '<input type="text" name="rodust_smtp_options[' . $field . ']" value="' . esc_attr($value) . '" class="regular-text" />';
                break;
        }
        
        // Dicas específicas
        switch ($field) {
            case 'smtp_host':
                echo '<p class="description">Ex: smtp.hostinger.com ou smtp.gmail.com</p>';
                break;
            case 'smtp_username':
                echo '<p class="description">Seu e-mail completo (ex: contato@rodust.com.br)</p>';
                break;
            case 'smtp_password':
                echo '<p class="description">Para Gmail, use App Password, não a senha normal</p>';
                break;
            case 'from_email':
                echo '<p class="description">E-mail que aparecerá como remetente</p>';
                break;
            case 'from_name':
                echo '<p class="description">Nome que aparecerá como remetente (ex: Rodust - Contato)</p>';
                break;
        }
    }
    
    /**
     * Página de administração
     */
    public function admin_page() {
        if (isset($_POST['test_email'])) {
            $this->send_test_email();
        }
        
        ?>
        <div class="wrap">
            <h1>Configurações SMTP - Rodust</h1>
            
            <?php 
            settings_errors(); 
            
            // Verifica se já está configurado automaticamente
            $options = get_option('rodust_smtp_options', array());
            if (!empty($options['smtp_host']) && $options['smtp_host'] === 'smtp.hostinger.com') {
                echo '<div class="notice notice-success">
                    <p><strong>✅ SMTP Configurado Automaticamente!</strong></p>
                    <p>As configurações da Hostinger foram aplicadas. O sistema está pronto para enviar e-mails.</p>
                    <p><strong>Envio:</strong> noreply@rodust.com.br → <strong>Recebimento:</strong> contato@rodust.com.br</p>
                </div>';
            }
            ?>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('rodust_smtp_settings');
                do_settings_sections('rodust-smtp');
                submit_button('Salvar Configurações');
                ?>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h2>Teste de E-mail</h2>
            <p>Envie um e-mail de teste para verificar se as configurações estão funcionando:</p>
            
            <form method="post" style="background: #f9f9f9; padding: 20px; border-radius: 5px; max-width: 500px;">
                <table class="form-table">
                    <tr>
                        <th scope="row">E-mail de Destino:</th>
                        <td>
                            <input type="email" name="test_email_to" class="regular-text" required 
                                   placeholder="seu-email@exemplo.com" />
                        </td>
                    </tr>
                </table>
                <?php submit_button('Enviar E-mail Teste', 'secondary', 'test_email'); ?>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <div class="notice notice-warning inline">
                <h4>🔧 Como configurar:</h4>
                <h4>Hostinger SMTP (Configuração Recomendada):</h4>
                <ul>
                    <li>Servidor: <code>smtp.hostinger.com</code></li>
                    <li>Porta: <code>587</code></li>
                    <li>Segurança: <code>TLS</code></li>
                    <li>Usuário: <code>noreply@rodust.com.br</code> (para envios automáticos)</li>
                    <li>Senha: Senha do e-mail noreply da Hostinger</li>
                    <li>Remetente: <code>noreply@rodust.com.br</code></li>
                    <li>Nome: <code>Rodust - Sistema</code></li>
                </ul>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0;">
                    <h4>💡 Estratégia Recomendada de E-mails:</h4>
                    <p><strong>contato@rodust.com.br:</strong> Para receber mensagens dos clientes</p>
                    <p><strong>noreply@rodust.com.br:</strong> Para envios automáticos do site</p>
                    <p>Os clientes recebem notificação do noreply@, mas podem responder para contato@</p>
                </div>
                
                <h4>Gmail SMTP:</h4>
                <ul>
                    <li>Servidor: <code>smtp.gmail.com</code></li>
                    <li>Porta: <code>587</code></li>
                    <li>Segurança: <code>TLS</code></li>
                    <li>Usuário: Seu e-mail Gmail completo</li>
                    <li>Senha: <strong>App Password</strong> (não a senha normal)</li>
                </ul>
                
                <p><strong>Para Gmail:</strong> Acesse <a href="https://support.google.com/accounts/answer/185833" target="_blank">Google App Passwords</a> para gerar uma senha específica.</p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Configura SMTP no PHPMailer
     */
    public function configure_smtp($phpmailer) {
        $options = get_option('rodust_smtp_options', array());
        
        // Verifica se as configurações estão definidas
        if (empty($options['smtp_host']) || empty($options['smtp_username'])) {
            return;
        }
        
        $phpmailer->isSMTP();
        $phpmailer->Host = $options['smtp_host'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $options['smtp_username'];
        $phpmailer->Password = $options['smtp_password'];
        $phpmailer->SMTPSecure = $options['smtp_secure'] ?: 'tls';
        $phpmailer->Port = $options['smtp_port'] ?: 587;
        
        // Configurações do remetente
        if (!empty($options['from_email'])) {
            $phpmailer->setFrom($options['from_email'], $options['from_name'] ?: get_bloginfo('name'));
        }
        
        // Debug (apenas para admins)
        if (current_user_can('manage_options') && isset($_GET['smtp_debug'])) {
            $phpmailer->SMTPDebug = 2;
        }
    }
    
    /**
     * Envia e-mail de teste
     */
    private function send_test_email() {
        if (!isset($_POST['test_email_to']) || !is_email($_POST['test_email_to'])) {
            add_settings_error('rodust_smtp', 'invalid_email', 'E-mail de destino inválido.', 'error');
            return;
        }
        
        $to = sanitize_email($_POST['test_email_to']);
        $subject = 'Teste SMTP - Rodust';
        $message = "
        <h2>✅ Teste de E-mail SMTP</h2>
        <p>Este é um e-mail de teste do sistema SMTP da Rodust.</p>
        <p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>
        <p><strong>Site:</strong> " . get_site_url() . "</p>
        <p>Se você recebeu este e-mail, o SMTP está funcionando corretamente! 🎉</p>
        
        <hr>
        <p><small>Rodust - Ferramentas e Parafusos</small></p>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('admin_email')
        );
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        if ($sent) {
            add_settings_error('rodust_smtp', 'test_success', 'E-mail de teste enviado com sucesso! Verifique sua caixa de entrada.', 'updated');
        } else {
            add_settings_error('rodust_smtp', 'test_error', 'Falha ao enviar e-mail de teste. Verifique as configurações SMTP.', 'error');
        }
    }
}

// Inicializa o plugin
new Rodust_SMTP();

/**
 * Função helper para outros plugins usarem
 */
function rodust_send_email($to, $subject, $message, $headers = array()) {
    return wp_mail($to, $subject, $message, $headers);
}