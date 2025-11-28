<?php
/**
 * Template Name: Webhook Receiver
 * 
 * Recebe webhooks do Bling e outros serviços
 * Registra logs e pode processar eventos
 * 
 * URL: https://localhost:8443/webhook
 */

// Desabilitar output buffering
if (ob_get_level()) {
    ob_end_clean();
}

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Bling-Signature');

// Responder OPTIONS rapidamente (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Capturar payload
$raw_body = file_get_contents('php://input');
$headers = getallheaders();
$method = $_SERVER['REQUEST_METHOD'];
$timestamp = date('Y-m-d H:i:s');

// Parse JSON
$data = json_decode($raw_body, true);

// Arquivo de log
$log_file = get_template_directory() . '/webhook.log';

// Preparar entrada de log
$log_entry = [
    'timestamp' => $timestamp,
    'method' => $method,
    'headers' => $headers,
    'body' => $data,
    'raw_body' => $raw_body,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
];

// Escrever no log
file_put_contents(
    $log_file,
    json_encode($log_entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n" . str_repeat('=', 100) . "\n\n",
    FILE_APPEND
);

// Processar webhook baseado na origem
$response = [
    'success' => true,
    'message' => 'Webhook recebido com sucesso',
    'timestamp' => $timestamp,
    'received_data' => $data,
];

// Identificar origem do webhook
if (isset($headers['X-Bling-Signature'])) {
    $response['source'] = 'Bling';
    $response['event'] = $data['event'] ?? 'unknown';
    
    // Aqui você pode adicionar lógica específica do Bling
    // Exemplos: pedido atualizado, produto criado, estoque alterado
    
} elseif (isset($data['test']) && $data['test'] === true) {
    $response['source'] = 'Test';
    $response['message'] = '🧪 Teste de webhook recebido! Sistema funcionando corretamente.';
    
} else {
    $response['source'] = 'Unknown';
}

// Log adicional para eventos importantes
if (isset($data['event'])) {
    error_log("[Webhook] {$response['source']}: {$data['event']} em {$timestamp}");
}

// Responder
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
