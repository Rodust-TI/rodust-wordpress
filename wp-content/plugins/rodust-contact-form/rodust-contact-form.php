<?php
/**
 * Plugin Name: Rodust Contact Form
 * Description: Processamento de formulário de contato para o tema Rodust com envio por email
 * Version: 1.0.0
 * Author: Rodust Theme
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class Rodust_Contact_Form {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Hook para processar formulário
        add_action('wp', array($this, 'process_contact_form'));
        
        // Adicionar menu admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Processa o formulário de contato
     */
    public function process_contact_form() {
        if (!isset($_POST['submit_contact']) || !wp_verify_nonce($_POST['contact_nonce'], 'rodust_contact_form')) {
            return;
        }
        
        // Sanitiza dados
        $name = sanitize_text_field($_POST['contact_name'] ?? '');
        $email = sanitize_email($_POST['contact_email'] ?? '');
        $phone = sanitize_text_field($_POST['contact_phone'] ?? '');
        $subject = sanitize_text_field($_POST['contact_subject'] ?? '');
        $message = sanitize_textarea_field($_POST['contact_message'] ?? '');
        
        // Validação
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            wp_redirect(add_query_arg('contact_status', 'error', wp_get_referer()));
            exit;
        }
        
        // Configurações de email usando estratégia de dois e-mails
        $settings = get_option('rodust_contact_settings', array());
        
        // E-mail que recebe as mensagens (contato@rodust.com.br)
        $receive_email = !empty($settings['admin_email']) ? $settings['admin_email'] : 'contato@rodust.com.br';
        
        // E-mail que envia (noreply@rodust.com.br) - configurado no SMTP
        $smtp_settings = get_option('rodust_smtp_options', array());
        $from_email = !empty($smtp_settings['from_email']) ? $smtp_settings['from_email'] : 'noreply@rodust.com.br';
        $from_name = !empty($smtp_settings['from_name']) ? $smtp_settings['from_name'] : 'Rodust - Sistema';
        
        // Monta o email
        $email_subject = '🔧 [Rodust] Nova mensagem: ' . $subject;
        $email_message = $this->build_email_message($name, $email, $phone, $subject, $message);
        
        // Headers do email - estratégia de dois e-mails
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
            'Reply-To: ' . $name . ' <' . $email . '>',
            'X-Mailer: Rodust Contact Form'
        );
        
        // Envia email para o e-mail de recebimento
        $sent = wp_mail($receive_email, $email_subject, $email_message, $headers);
        
        if ($sent) {
            // Salva contato no banco (opcional)
            $this->save_contact($name, $email, $phone, $subject, $message);
            
            wp_redirect(add_query_arg('contact_status', 'success', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('contact_status', 'failed', wp_get_referer()));
        }
        
        exit;
    }
    
    /**
     * Constrói a mensagem do email
     */
    private function build_email_message($name, $email, $phone, $subject, $message) {
        $template = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; }
                .header { background: #1d2327; color: white; padding: 20px; text-align: center; }
                .content { background: white; padding: 30px; margin: 20px; border-radius: 8px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .value { margin-top: 5px; padding: 10px; background: #f5f5f5; border-left: 3px solid #007cba; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🔧 Novo Contato - Rodust</h2>
                </div>
                <div class="content">
                    <div class="field">
                        <div class="label">👤 Nome:</div>
                        <div class="value">' . esc_html($name) . '</div>
                    </div>
                    <div class="field">
                        <div class="label">📧 Email:</div>
                        <div class="value"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></div>
                    </div>
                    <div class="field">
                        <div class="label">📱 Telefone:</div>
                        <div class="value">' . esc_html($phone ?: 'Não informado') . '</div>
                    </div>
                    <div class="field">
                        <div class="label">📋 Assunto:</div>
                        <div class="value">' . esc_html($subject) . '</div>
                    </div>
                    <div class="field">
                        <div class="label">💬 Mensagem:</div>
                        <div class="value">' . nl2br(esc_html($message)) . '</div>
                    </div>
                </div>
                <div class="footer">
                    <p>Enviado em ' . date('d/m/Y às H:i') . ' pelo site ' . home_url() . '</p>
                    <p>🔧 Rodust - Ferramentas e Parafusos</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $template;
    }
    
    /**
     * Salva contato no banco de dados (opcional)
     */
    private function save_contact($name, $email, $phone, $subject, $message) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'rodust_contacts';
        
        // Cria tabela se não existir
        $this->create_contacts_table();
        
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'created_at' => current_time('mysql'),
                'status' => 'new'
            )
        );
    }
    
    /**
     * Cria tabela de contatos
     */
    private function create_contacts_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'rodust_contacts';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            subject varchar(200) NOT NULL,
            message text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'new',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Adiciona menu admin
     */
    public function add_admin_menu() {
        add_options_page(
            'Configurações de Contato',
            'Contato Rodust',
            'manage_options',
            'rodust-contact-settings',
            array($this, 'admin_page')
        );
        
        add_menu_page(
            'Contatos Recebidos',
            'Contatos',
            'manage_options',
            'rodust-contacts',
            array($this, 'contacts_page'),
            'dashicons-email-alt',
            30
        );
    }
    
    /**
     * Registra configurações
     */
    public function register_settings() {
        register_setting('rodust_contact_group', 'rodust_contact_settings');
    }
    
    /**
     * Página de configurações
     */
    public function admin_page() {
        $settings = get_option('rodust_contact_settings', array());
        ?>
        <div class="wrap">
            <h1>⚙️ Configurações do Formulário de Contato</h1>
            <form method="post" action="options.php">
                <?php settings_fields('rodust_contact_group'); ?>
                <table class="form-table">
                    <tr>
                        <th>Email de Destino</th>
                        <td>
                            <input type="email" name="rodust_contact_settings[admin_email]" 
                                   value="<?php echo esc_attr($settings['admin_email'] ?? get_option('admin_email')); ?>" 
                                   class="regular-text">
                            <p class="description">Email que receberá as mensagens de contato</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Nome do Remetente</th>
                        <td>
                            <input type="text" name="rodust_contact_settings[from_name]" 
                                   value="<?php echo esc_attr($settings['from_name'] ?? get_bloginfo('name')); ?>" 
                                   class="regular-text">
                            <p class="description">Nome que aparecerá como remetente</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Página de contatos recebidos
     */
    public function contacts_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'rodust_contacts';
        $contacts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 50");
        
        ?>
        <div class="wrap">
            <h1>📧 Contatos Recebidos</h1>
            <?php if ($contacts) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Assunto</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($contact->name); ?></strong></td>
                                <td><a href="mailto:<?php echo esc_attr($contact->email); ?>"><?php echo esc_html($contact->email); ?></a></td>
                                <td><?php echo esc_html($contact->subject); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($contact->created_at)); ?></td>
                                <td>
                                    <span class="<?php echo $contact->status === 'new' ? 'button-primary' : 'button-secondary'; ?> button-small">
                                        <?php echo $contact->status === 'new' ? '🆕 Novo' : '✅ Lido'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>📭 Nenhum contato recebido ainda.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Inicializa o plugin
new Rodust_Contact_Form();
?>