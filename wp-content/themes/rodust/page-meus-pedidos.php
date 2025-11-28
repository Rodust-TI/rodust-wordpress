<?php
/**
 * Template Name: Meus Pedidos
 */

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Meus Pedidos</h1>
        <p class="text-gray-600">Acompanhe o status dos seus pedidos</p>
    </div>

    <!-- Mensagem de não autenticado -->
    <div id="not-authenticated" class="hidden bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded mb-8">
        <p class="text-yellow-700 mb-4">Você precisa estar logado para ver seus pedidos.</p>
        <a href="<?php echo home_url('/login'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
            Fazer Login
        </a>
    </div>

    <!-- Lista de pedidos -->
    <div id="orders-area" class="hidden">
        
        <!-- Loading -->
        <div id="loading-orders" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Carregando pedidos...</p>
        </div>

        <!-- Lista vazia -->
        <div id="no-orders" class="hidden text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum pedido encontrado</h3>
            <p class="text-gray-600 mb-6">Você ainda não fez nenhum pedido.</p>
            <a href="<?php echo home_url('/produtos'); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
                Começar a Comprar
            </a>
        </div>

        <!-- Tabela de pedidos -->
        <div id="orders-list" class="hidden">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pedido
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody id="orders-tbody" class="bg-white divide-y divide-gray-200">
                        <!-- Pedidos serão inseridos aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = sessionStorage.getItem('customer_token');
    
    if (!token) {
        // Usuário não autenticado
        document.getElementById('not-authenticated').classList.remove('hidden');
        return;
    }
    
    // Usuário autenticado - mostrar área de pedidos
    document.getElementById('orders-area').classList.remove('hidden');
    
    // Buscar pedidos da API
    fetch(window.RODUST_API_URL + '/api/orders', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-orders').classList.add('hidden');
        
        if (!data.orders || data.orders.length === 0) {
            document.getElementById('no-orders').classList.remove('hidden');
            return;
        }
        
        // Renderizar pedidos
        const tbody = document.getElementById('orders-tbody');
        data.orders.forEach(order => {
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'processing': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            
            const statusLabels = {
                'pending': 'Pendente',
                'processing': 'Em processamento',
                'completed': 'Concluído',
                'cancelled': 'Cancelado'
            };
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #${order.id}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${new Date(order.created_at).toLocaleDateString('pt-BR')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                        ${statusLabels[order.status] || order.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="/pedido/${order.id}" class="text-blue-600 hover:text-blue-900">
                        Ver detalhes
                    </a>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        document.getElementById('orders-list').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Erro ao carregar pedidos:', error);
        document.getElementById('loading-orders').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <p class="text-red-700">Erro ao carregar pedidos. Tente novamente mais tarde.</p>
            </div>
        `;
    });
});
</script>

<?php get_footer(); ?>
