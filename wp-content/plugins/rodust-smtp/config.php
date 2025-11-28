<?php
/**
 * Configurações SMTP - Rodust
 * Arquivo de configuração para não expor credenciais no código
 */

// Configurações SMTP da Hostinger
define('RODUST_SMTP_HOST', 'smtp.hostinger.com');
define('RODUST_SMTP_PORT', 587);
define('RODUST_SMTP_SECURE', 'tls');
define('RODUST_SMTP_USERNAME', 'noreply@rodust.com.br');
define('RODUST_SMTP_PASSWORD', ',)v6B1E,WoQQ2\Xf');
define('RODUST_SMTP_FROM_EMAIL', 'noreply@rodust.com.br');
define('RODUST_SMTP_FROM_NAME', 'Rodust - Sistema');

// E-mail que recebe as mensagens dos formulários
define('RODUST_CONTACT_EMAIL', 'contato@rodust.com.br');