<?php
/**
* Template Name: Página de Contato
* 
* Template personalizado para página de contato da Rodust
*/
get_header(); ?>

<main class="contact-page">

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-50 to-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Entre em Contato
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    Estamos aqui para ajudar com suas necessidades de ferramentas e parafusos.
                    Entre em contato conosco e tire suas dúvidas!
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">

                <!-- Informações de Contato -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"> 
                            Nossos Dados
                        </h2>

                        <div class="space-y-6">
                            <!-- Telefone -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition-colors">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Telefone</h3>
                                    <a href="tel:+5519992015005" class="text-green-600 hover:text-green-800 font-medium">
                                        +55 19 99201-5005
                                    </a>
                                    <p class="text-gray-600 text-sm mt-1">Segunda à Sexta: 8h às 18h</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">E-mail</h3>
                                    <a href="mailto:contato@rodust.com.br" class="text-blue-600 hover:text-blue-800 font-medium">
                                        contato@rodust.com.br
                                    </a>
                                    <p class="text-gray-600 text-sm mt-1">Respondemos em até 24h</p>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-purple-50 transition-colors">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Endereço</h3>
                                    <address class="text-gray-700 not-italic leading-relaxed">
                                        R. Noel Rosa, 65<br>
                                        Higienópolis, Piracicaba - SP<br>
                                        CEP: 13424-371
                                    </address>
                                    <a href="https://maps.google.com/?q=R.+Noel+Rosa,+65+-+Higienópolis,+Piracicaba+-+SP,+13424-371"
                                        target="_blank"
                                        class="text-purple-600 hover:text-purple-800 text-sm font-medium inline-flex items-center mt-2">
                                        Ver no mapa
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Adicional -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                            Rodust - Ferramentas e Parafusos
                        </h3>
                        <p class="text-blue-800 text-sm leading-relaxed">
                            Especialistas em ferramentas e parafusos de qualidade.
                            Em breve, nossa loja online estará disponível com catálogo completo
                            e facilidade de compra. Fique atento!
                        </p>
                    </div>
                </div>

                <!-- Formulário de Contato -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        Envie sua Mensagem
                    </h2>

                    <form id="contact-form" class="space-y-6" method="post" action="#contact-form">
                        <?php wp_nonce_field('rodust_contact_form', 'contact_nonce'); ?>

                        <!-- Nome e Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome Completo *
                                </label>
                                <input type="text"
                                    id="contact_name"
                                    name="contact_name"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="Seu nome">
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    E-mail *
                                </label>
                                <input type="email"
                                    id="contact_email"
                                    name="contact_email"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="seu@email.com">
                            </div>
                        </div>

                        <!-- Telefone e Assunto -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefone
                                </label>
                                <input type="tel"
                                    id="contact_phone"
                                    name="contact_phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="(19) 99999-9999">
                            </div>
                            <div>
                                <label for="contact_subject" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assunto *
                                </label>
                                <select id="contact_subject"
                                    name="contact_subject"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">Selecione um assunto</option>
                                    <option value="Orçamento">Solicitar Orçamento</option>
                                    <option value="Produto">Dúvidas sobre Produtos</option>
                                    <option value="Suporte">Suporte Técnico</option>
                                    <option value="Parceria">Parceria Comercial</option>
                                    <option value="Outro">Outro Assunto</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mensagem -->
                        <div>
                            <label for="contact_message" class="block text-sm font-medium text-gray-700 mb-2">
                                Mensagem *
                            </label>
                            <textarea id="contact_message"
                                name="contact_message"
                                required
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-vertical"
                                placeholder="Descreva sua necessidade, dúvida ou solicitação..."></textarea>
                        </div>

                        <!-- Botão Submit -->
                        <div>
                            <button type="submit"
                                name="submit_contact"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 hover:shadow-lg flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Mensagem
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 text-center">
                            * Campos obrigatórios. Responderemos o mais breve possível.
                        </p>
                    </form>

                    <!-- Mensagens de Status -->
                    <div id="form-messages" class="mt-4">
                        <?php
                        // Mostra mensagens baseadas no parâmetro da URL
                        if (isset($_GET['contact_status'])) {
                            switch ($_GET['contact_status']) {
                                case 'success':
                                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                                        ✅ <strong>Mensagem enviada com sucesso!</strong><br>
                                        Obrigado pelo contato. Responderemos em breve.
                                    </div>';
                                    break;
                                case 'error':
                                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                                        ❌ <strong>Erro:</strong> Por favor, preencha todos os campos obrigatórios.
                                    </div>';
                                    break;
                                case 'failed':
                                    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                                        ⚠️ <strong>Falha no envio:</strong> Tente novamente ou entre em contato diretamente.
                                    </div>';
                                    break;
                            }
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Mapa Google Maps Embed -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">Nossa Localização</h3>
                <p class="text-gray-600">R. Noel Rosa, 65 - Higienópolis, Piracicaba - SP</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="relative rounded-lg overflow-hidden shadow-lg bg-white p-2">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3679.365066945971!2d-47.64347772469425!3d-22.751829379364533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c6305e9c0826fb%3A0xba7ce58e5577fea8!2sR.%20Noel%20Rosa%2C%2065%20-%20Higien%C3%B3polis%2C%20Piracicaba%20-%20SP%2C%2013424-371!5e0!3m2!1spt-BR!2sbr!4v1762887742465!5m2!1spt-BR!2sbr"
                        width="100%"
                        height="400"
                        style="border:0; border-radius: 8px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Localização da Rodust - Ferramentas e Parafusos">
                    </iframe>
                </div>

                <div class="text-center mt-4">
                    <a href="https://maps.app.goo.gl/X5iTn6NTZiZr93Df8"
                        target="_blank"
                        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium bg-white px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Abrir no Google Maps
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>



<?php get_footer(); ?>