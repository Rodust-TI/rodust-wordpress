<?php
/**
 * Template Name: Reenviar Confirmação
 */

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    <div class="max-w-md mx-auto">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Reenviar Email de Confirmação</h1>
            <p class="text-gray-600">Não recebeu o email? Digite seu email abaixo para receber um novo link.</p>
        </div>

        <!-- Formulário -->
        <form id="resend-form" class="space-y-4" onsubmit="return false;">
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="seu@email.com">
            </div>

            <!-- Mensagem de feedback -->
            <div id="feedback-message" class="hidden"></div>

            <button type="submit" 
                    id="submit-btn"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                Reenviar Email
            </button>

        </form>

        <!-- Links úteis -->
        <div class="mt-6 text-center text-sm">
            <a href="<?php echo home_url('/cadastro'); ?>" class="text-blue-600 hover:underline">
                Criar nova conta
            </a>
            <span class="text-gray-400 mx-2">|</span>
            <a href="<?php echo home_url('/login'); ?>" class="text-blue-600 hover:underline">
                Fazer login
            </a>
        </div>

        <!-- Dicas -->
        <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="font-medium text-gray-900 mb-2">💡 Não encontrou o email?</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Verifique sua caixa de <strong>spam</strong> ou <strong>lixo eletrônico</strong></li>
                <li>• Aguarde alguns minutos - emails podem demorar</li>
                <li>• Certifique-se de digitar o email correto</li>
                <li>• Adicione <strong>noreply@rodust.com.br</strong> aos seus contatos</li>
            </ul>
        </div>

    </div>
</main>

<script>
jQuery(document).ready(function($) {
    
    $('#resend-form').on('submit', function(e) {
        e.preventDefault();

        const email = $('#email').val().trim();
        const $btn = $('#submit-btn');
        const $feedback = $('#feedback-message');
        
        // Desabilitar botão
        $btn.prop('disabled', true).text('Enviando...');
        $feedback.addClass('hidden');

        // Fazer requisição
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/resend-verification',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            crossDomain: true,
            xhrFields: { withCredentials: true },
            data: JSON.stringify({ email: email }),
            success: function(response) {
                showFeedback('success', response.message || 'Email enviado com sucesso! Verifique sua caixa de entrada.');
                $btn.prop('disabled', false).text('Reenviar Email');
                $('#email').val(''); // Limpar campo
            },
            error: function(xhr) {
                let errorMsg = 'Erro ao enviar email. Tente novamente.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 400) {
                    errorMsg = 'Email já verificado ou não encontrado.';
                } else if (xhr.status === 422) {
                    // Erros de validação
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.email) {
                        errorMsg = errors.email[0];
                    }
                }
                
                showFeedback('error', errorMsg);
                $btn.prop('disabled', false).text('Reenviar Email');
            }
        });
    });

    function showFeedback(type, message) {
        const $feedback = $('#feedback-message');
        const bgColor = type === 'success' 
            ? 'bg-green-100 text-green-700 border-green-500' 
            : 'bg-red-100 text-red-700 border-red-500';
        
        $feedback
            .removeClass('hidden bg-green-100 bg-red-100 text-green-700 text-red-700 border-green-500 border-red-500')
            .addClass(bgColor + ' border-l-4 p-4 rounded text-sm')
            .html(message);
    }
});
</script>

<?php get_footer(); ?>
