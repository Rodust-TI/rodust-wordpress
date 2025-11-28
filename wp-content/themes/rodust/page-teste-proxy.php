<?php
/**
 * Template Name: Teste API Proxy
 * 
 * Página de testes para verificar se o proxy está funcionando corretamente
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste API Proxy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #d1ecf1; border-color: #bee5eb; }
        button {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }
        button:hover { background: #0056b3; }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Teste de API Proxy - Rodust</h1>
    
    <div class="test-section info">
        <h3>🔗 Configuração Atual</h3>
        <p><strong>API URL:</strong> <code id="current-api-url">Carregando...</code></p>
        <p><strong>WordPress Home:</strong> <code><?php echo home_url(); ?></code></p>
        <p><strong>Proxy Endpoint:</strong> <code><?php echo home_url('/wp-json/rodust-proxy/v1'); ?></code></p>
    </div>

    <div class="test-section">
        <h3>🧪 Testes Disponíveis</h3>
        <button onclick="testProxyHealth()">1. Testar Proxy (Health Check)</button>
        <button onclick="testDirectAPI()">2. Testar API Direta (Mixed Content)</button>
        <button onclick="testProxyAPI()">3. Testar API via Proxy (Correto)</button>
        <button onclick="testWithAuth()">4. Testar com Autenticação</button>
    </div>

    <div id="results"></div>

    <script>
        // Exibir configuração atual
        document.getElementById('current-api-url').textContent = window.RODUST_API_URL || 'NÃO DEFINIDO!';

        function addResult(title, content, type = 'info') {
            const resultsDiv = document.getElementById('results');
            const section = document.createElement('div');
            section.className = 'test-section ' + type;
            section.innerHTML = `
                <h4>${title}</h4>
                <pre>${JSON.stringify(content, null, 2)}</pre>
            `;
            resultsDiv.insertBefore(section, resultsDiv.firstChild);
        }

        // Teste 1: Health Check do Proxy
        async function testProxyHealth() {
            try {
                const response = await fetch('<?php echo home_url('/wp-json/rodust-proxy/v1/test'); ?>');
                const data = await response.json();
                addResult('✅ Teste 1: Proxy Health Check', data, 'success');
            } catch (error) {
                addResult('❌ Teste 1: Proxy Health Check', { error: error.message }, 'error');
            }
        }

        // Teste 2: API Direta (vai dar erro de Mixed Content)
        async function testDirectAPI() {
            try {
                const response = await fetch('http://laravel.test/api/products?per_page=1');
                const data = await response.json();
                addResult('⚠️ Teste 2: API Direta', { 
                    message: 'SUCESSO (inesperado - deveria bloquear Mixed Content)',
                    data: data 
                }, 'success');
            } catch (error) {
                addResult('✅ Teste 2: API Direta (Bloqueado)', { 
                    message: 'Mixed Content bloqueado corretamente',
                    error: error.message 
                }, 'success');
            }
        }

        // Teste 3: API via Proxy (deve funcionar)
        async function testProxyAPI() {
            try {
                const url = window.RODUST_API_URL + '/api/products?per_page=1';
                console.log('Testing URL:', url);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                addResult('✅ Teste 3: API via Proxy', {
                    url: url,
                    status: response.status,
                    data: data
                }, 'success');
            } catch (error) {
                addResult('❌ Teste 3: API via Proxy', { 
                    url: window.RODUST_API_URL + '/api/products?per_page=1',
                    error: error.message 
                }, 'error');
            }
        }

        // Teste 4: Com Autenticação
        async function testWithAuth() {
            const token = sessionStorage.getItem('customer_token');
            
            if (!token) {
                addResult('⚠️ Teste 4: Autenticação', { 
                    message: 'Nenhum token encontrado. Faça login primeiro.',
                    note: 'Este teste requer que você esteja logado.'
                }, 'error');
                return;
            }

            try {
                const response = await fetch(window.RODUST_API_URL + '/api/customers/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                addResult('✅ Teste 4: API com Autenticação', {
                    status: response.status,
                    data: data
                }, response.ok ? 'success' : 'error');
            } catch (error) {
                addResult('❌ Teste 4: API com Autenticação', { error: error.message }, 'error');
            }
        }

        // Executar teste básico ao carregar
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if (window.RODUST_API_URL) {
                    testProxyHealth();
                } else {
                    addResult('❌ Configuração', { 
                        error: 'window.RODUST_API_URL não está definido!',
                        note: 'Verifique se o functions.php está carregando corretamente.'
                    }, 'error');
                }
            }, 500);
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
