/**
 * Configuração de URLs do Rodust Ecommerce
 * 
 * Adicione estas constantes no wp-config.php para centralizar URLs
 * e facilitar a migração entre ambientes (local/staging/production)
 * 
 * =====================================================================
 * INSTRUÇÕES:
 * =====================================================================
 * 
 * 1. Copie as linhas abaixo para o seu wp-config.php
 * 2. Cole ANTES da linha: require_once ABSPATH . 'wp-settings.php';
 * 3. Ajuste os valores conforme seu ambiente
 * 
 * =====================================================================
 * EXEMPLO PARA DESENVOLVIMENTO LOCAL (Docker):
 * =====================================================================
 */

// URL da API Laravel
define('RODUST_API_URL', 'http://localhost:8000/api');

// Ou configure por partes:
// define('RODUST_API_HOST', 'localhost');
// define('RODUST_API_PORT', '8000');

/**
 * =====================================================================
 * EXEMPLO PARA STAGING:
 * =====================================================================
 */

// define('RODUST_API_URL', 'https://api-staging.rodust.com.br/api');

/**
 * =====================================================================
 * EXEMPLO PARA PRODUÇÃO:
 * =====================================================================
 */

// define('RODUST_API_URL', 'https://api.rodust.com.br/api');

/**
 * =====================================================================
 * NOTAS IMPORTANTES:
 * =====================================================================
 * 
 * 1. Se RODUST_API_URL estiver definida no wp-config.php, ela tem
 *    PRIORIDADE sobre as configurações do plugin no admin
 * 
 * 2. Isso permite controlar URLs por ambiente sem alterar o banco
 * 
 * 3. Em produção, SEMPRE use HTTPS para segurança
 * 
 * 4. O plugin funciona sem essas constantes (usa settings do admin
 *    ou fallback para localhost)
 * 
 * =====================================================================
 */
