<?php
/**
 * Template Name: Meus Pedidos
 * 
 * IMPORTANTE: Esta página redireciona para /minha-conta?tab=pedidos
 * pois os pedidos são gerenciados dentro da área "Minha Conta"
 */

// Redirecionar para minha conta com aba de pedidos
wp_redirect(home_url('/minha-conta?tab=pedidos'));
exit;
?>
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
