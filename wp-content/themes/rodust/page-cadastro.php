<?php
/**
 * Template Name: Cadastro
 */

// Redirecionar se já estiver logado
if (isset($_SESSION['customer_token'])) {
    wp_redirect(home_url('/minha-conta'));
    exit;
}

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    <div class="max-w-md mx-auto">
        
        <!-- Cabeçalho -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Criar Conta</h1>
            <p class="text-gray-600">Preencha os dados abaixo para se cadastrar</p>
        </div>

        <!-- Mensagens -->
        <div id="register-messages" class="mb-6 hidden"></div>

        <!-- Loader de Cadastro -->
        <div id="register-loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 flex flex-col items-center">
                <svg class="animate-spin h-12 w-12 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Enviando cadastro...</p>
                <p class="text-gray-500 text-sm mt-2">Por favor, aguarde</p>
            </div>
        </div>

        <!-- Formulário de Cadastro -->
        <form id="register-form" class="bg-white rounded-lg shadow-md p-8" onsubmit="return false;">
            
            <!-- Nome Completo -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome Completo *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Seu nome completo">
            </div>

            <!-- CPF -->
            <div class="mb-6">
                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                    CPF *
                </label>
                <input type="text" 
                       id="cpf" 
                       name="cpf" 
                       required
                       maxlength="14"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="000.000.000-00">
                <p class="text-xs text-gray-500 mt-1">Digite apenas números</p>
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    E-mail *
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
                    Senha *
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       minlength="6"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Mínimo 6 caracteres">
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirmar Senha *
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       minlength="6"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Digite a senha novamente">
            </div>

            <!-- Termos -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           required
                           class="mt-1 mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">
                        Eu aceito os <a href="#" class="text-blue-600 hover:underline">Termos de Uso</a> 
                        e a <a href="#" class="text-blue-600 hover:underline">Política de Privacidade</a>
                    </span>
                </label>
            </div>

            <!-- Botão Cadastrar -->
            <button type="submit" 
                    id="register-btn"
                    class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Criar Conta
            </button>

        </form>

        <!-- Link para Login -->
        <div class="text-center mt-6">
            <p class="text-gray-600">
                Já possui uma conta? 
                <a href="<?php echo home_url('/login'); ?>" class="text-blue-600 hover:underline font-medium">Faça login</a>
            </p>
        </div>

    </div>
</main>

<script>
console.log('Script de cadastro carregado');
console.log('jQuery disponível:', typeof jQuery !== 'undefined');

jQuery(document).ready(function($) {
    console.log('jQuery ready executado');
    
    // Máscara de CPF
    $('#cpf').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        
        $(this).val(value);
    });

    // Submit do formulário
    $('#register-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $('#register-btn');
        const $messages = $('#register-messages');
        const $loader = $('#register-loader');

        // Validar senhas
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirmation').val();

        if (password !== passwordConfirm) {
            showMessage('error', 'As senhas não conferem.');
            return;
        }

        // Exibir loader
        $loader.removeClass('hidden');
        
        // Desabilitar botão
        $btn.prop('disabled', true).text('Cadastrando...');
        $messages.addClass('hidden');

        // Preparar dados
        const cpf = $('#cpf').val().replace(/\D/g, '');
        const data = {
            name: $('#name').val(),
            cpf: cpf,
            email: $('#email').val(),
            password: password,
            password_confirmation: passwordConfirm,
        };

        // Fazer requisição para API Laravel
        $.ajax({
            url: window.RODUST_API_URL + '/api/customers/register',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            data: JSON.stringify(data),
            success: function(response) {
                // Ocultar loader
                $loader.addClass('hidden');
                
                if (response.success) {
                    // NÃO salvar token ainda (email não verificado)
                    
                    showMessage('success', '<strong>Cadastro realizado!</strong><br>Enviamos um email de confirmação para <strong>' + $('#email').val() + '</strong>.<br>Por favor, verifique sua caixa de entrada e clique no link para ativar sua conta.');

                    // Desabilitar form
                    $form.find('input, button').prop('disabled', true);
                    $btn.text('Email Enviado ✓');

                } else {
                    showMessage('error', response.message || 'Erro ao realizar cadastro.');
                    $btn.prop('disabled', false).text('Criar Conta');
                }
            },
            error: function(xhr) {
                // Ocultar loader
                $loader.addClass('hidden');
                
                let errorMsg = 'Erro ao realizar cadastro.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                showMessage('error', errorMsg);
                $btn.prop('disabled', false).text('Criar Conta');
            }
        });
    });

    function showMessage(type, message) {
        const $messages = $('#register-messages');
        const bgColor = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';

        $messages
            .removeClass('hidden bg-green-100 bg-red-100 border-green-500 border-red-500 text-green-700 text-red-700')
            .addClass(bgColor + ' border-l-4 p-4 rounded')
            .html(message);
    }
});
</script>

<?php get_footer(); ?>
