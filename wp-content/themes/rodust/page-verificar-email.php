<?php
/**
 * Template Name: Verificar Email
 */

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    <div class="max-w-md mx-auto text-center">
        
        <!-- Loading State -->
        <div id="verifying-state">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verificando seu email...</h1>
            <p class="text-gray-600">Aguarde enquanto confirmamos seu cadastro.</p>
        </div>

        <!-- Success State -->
        <div id="success-state" class="hidden">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Email Verificado!</h1>
            <p class="text-gray-600 mb-6">Sua conta foi ativada com sucesso.</p>
            <p class="text-sm text-gray-500 mb-4">Redirecionando para sua conta...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Erro na Verificação</h1>
            <p class="text-gray-600 mb-6" id="error-message">Link inválido ou expirado.</p>
            
            <!-- Formulário para reenviar -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                <p class="text-sm text-gray-700 mb-4">Digite seu email para receber um novo link:</p>
                <form id="resend-form" class="flex gap-2">
                    <input type="email" 
                           id="resend-email" 
                           required
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="seu@email.com">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 whitespace-nowrap">
                        Reenviar
                    </button>
                </form>
                <div id="resend-message" class="mt-4 hidden"></div>
            </div>

            <a href="<?php echo home_url('/cadastro'); ?>" 
               class="inline-block mt-6 text-blue-600 hover:underline">
                Ou faça um novo cadastro
            </a>
        </div>

    </div>
</main>

<script>
jQuery(document).ready(function($) {
    
    // Pegar token da URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (!token) {
        showError('Token não fornecido.');
        return;
    }

    // Verificar token
    $.ajax({
        url: window.RODUST_API_URL + '/api/customers/verify-email',
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        crossDomain: true,
        xhrFields: { withCredentials: true },
        data: JSON.stringify({ token: token }),
        success: function(response) {
            if (response.success) {
                // Salvar token de autenticação (ambos os nomes para compatibilidade)
                sessionStorage.setItem('customer_token', response.data.token);
                sessionStorage.setItem('auth_token', response.data.token);
                sessionStorage.setItem('customer_data', JSON.stringify(response.data.customer));

                // Mostrar sucesso
                $('#verifying-state').addClass('hidden');
                $('#success-state').removeClass('hidden');

                // Redirecionar após 2s para login com parâmetro de confirmação
                setTimeout(function() {
                    window.location.href = '<?php echo home_url('/login?confirm=1'); ?>';
                }, 2000);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Token inválido ou expirado.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showError(errorMsg);
        }
    });

    // Reenviar email
    $('#resend-form').on('submit', function(e) {
        e.preventDefault();

        const email = $('#resend-email').val();
        const $btn = $(this).find('button');
        
        $btn.prop('disabled', true).text('Enviando...');

        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/resend-verification',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            crossDomain: true,
            xhrFields: { withCredentials: true },
            data: JSON.stringify({ email: email }),
            success: function(response) {
                showResendMessage('success', response.message || 'Email reenviado com sucesso!');
                $btn.prop('disabled', false).text('Reenviar');
            },
            error: function(xhr) {
                let errorMsg = 'Erro ao reenviar email.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showResendMessage('error', errorMsg);
                $btn.prop('disabled', false).text('Reenviar');
            }
        });
    });

    function showError(message) {
        $('#verifying-state').addClass('hidden');
        $('#error-state').removeClass('hidden');
        $('#error-message').text(message);
    }

    function showResendMessage(type, message) {
        const $msg = $('#resend-message');
        const bgColor = type === 'success' ? 'bg-green-100 text-green-700 border-green-500' : 'bg-red-100 text-red-700 border-red-500';
        
        $msg
            .removeClass('hidden bg-green-100 bg-red-100 text-green-700 text-red-700 border-green-500 border-red-500')
            .addClass(bgColor + ' border-l-4 p-3 rounded text-sm')
            .text(message);
    }
});
</script>

<?php get_footer(); ?>
