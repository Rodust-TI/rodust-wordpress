<?php
/**
 * Template Name: Login
 */

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    <div class="max-w-md mx-auto">
        
        <!-- Cabeçalho -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Entrar</h1>
            <p class="text-gray-600">Acesse sua conta</p>
        </div>

        <!-- Mensagens -->
        <div id="login-messages" class="mb-6 hidden"></div>

        <!-- Formulário de Login -->
        <form id="login-form" class="bg-white rounded-lg shadow-md p-8" onsubmit="return false;">
            
            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    E-mail
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="seu@email.com">
            </div>

            <!-- Senha -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Senha
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Sua senha">
            </div>

            <!-- Lembrar / Esqueceu -->
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="remember" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                </label>
                <a href="#" class="text-sm text-blue-600 hover:underline">Esqueceu a senha?</a>
            </div>

            <!-- Botão Entrar -->
            <button type="submit" 
                    id="login-btn"
                    class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Entrar
            </button>

        </form>

        <!-- Link para Cadastro -->
        <div class="text-center mt-6 space-y-2">
            <p class="text-gray-600">
                Não possui uma conta? 
                <a href="<?php echo home_url('/cadastro'); ?>" class="text-blue-600 hover:underline font-medium">Cadastre-se</a>
            </p>
            <p class="text-sm text-gray-500">
                Não recebeu o email de confirmação? 
                <a href="<?php echo home_url('/reenviar-confirmacao'); ?>" class="text-blue-600 hover:underline">Reenviar</a>
            </p>
        </div>

    </div>
</main>

<script>
jQuery(document).ready(function($) {
    
    // Verificar se já está logado
    const token = sessionStorage.getItem('customer_token');
    if (token) {
        // Verificar se veio da página de confirmação
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('confirm') === '1') {
            // Redirecionar para minha conta (completar cadastro)
            window.location.href = '<?php echo home_url('/minha-conta'); ?>';
        } else {
            // Redirecionar para produtos (já estava navegando)
            window.location.href = '<?php echo home_url('/produtos'); ?>';
        }
        return;
    }
    
    // Submit do formulário
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $('#login-btn');
        const $messages = $('#login-messages');

        // Desabilitar botão
        $btn.prop('disabled', true).text('Entrando...');
        $messages.addClass('hidden');

        // Preparar dados
        const data = {
            email: $('#email').val(),
            password: $('#password').val(),
        };

        // Fazer requisição para API Laravel
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/login',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    // Salvar token (ambos os nomes para compatibilidade)
                    sessionStorage.setItem('customer_token', response.data.token);
                    sessionStorage.setItem('auth_token', response.data.token);
                    sessionStorage.setItem('customer_data', JSON.stringify(response.data.customer));

                    showMessage('success', response.message || 'Login realizado com sucesso!');

                    // Determinar para onde redirecionar
                    let redirectUrl = '<?php echo home_url('/produtos'); ?>';
                    
                    // Verificar se há URL salva para redirect
                    const savedRedirect = sessionStorage.getItem('redirect_after_login');
                    if (savedRedirect) {
                        redirectUrl = savedRedirect;
                        sessionStorage.removeItem('redirect_after_login');
                    }
                    // Se veio da página de confirmação de email (query string confirm=1)
                    else if (window.location.search.includes('confirm=1')) {
                        redirectUrl = '<?php echo home_url('/minha-conta'); ?>';
                    }

                    // Redirecionar após 1s
                    setTimeout(function() {
                        window.location.href = redirectUrl;
                    }, 1000);
                } else {
                    showMessage('error', response.message || 'Erro ao fazer login.');
                    $btn.prop('disabled', false).text('Entrar');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Email ou senha incorretos.';
                let showResendLink = false;

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                    
                    // Verificar se é erro de email não verificado
                    if (errorMsg.includes('confirme seu email') || errorMsg.includes('verificado')) {
                        showResendLink = true;
                        errorMsg += '<br><br><a href="<?php echo home_url('/reenviar-confirmacao'); ?>" class="font-medium underline">Clique aqui para reenviar o email de confirmação</a>';
                    }
                }

                showMessage('error', errorMsg);
                $btn.prop('disabled', false).text('Entrar');
            }
        });
    });

    function showMessage(type, message) {
        const $messages = $('#login-messages');
        const bgColor = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';

        $messages
            .removeClass('hidden bg-green-100 bg-red-100 border-green-500 border-red-500 text-green-700 text-red-700')
            .addClass(bgColor + ' border-l-4 p-4 rounded')
            .html(message);
    }
});
</script>

<?php get_footer(); ?>
