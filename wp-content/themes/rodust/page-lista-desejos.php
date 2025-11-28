<?php
/**
 * Template Name: Lista de Desejos
 */

get_header();
?>

<main class="container mx-auto px-4 py-12 md:py-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">❤️ Minha Lista de Desejos</h1>
        <p class="text-gray-600">Produtos que você salvou para comprar depois</p>
    </div>

    <!-- Mensagem de não autenticado -->
    <div id="not-authenticated" class="hidden bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded mb-8">
        <p class="text-yellow-700 mb-4">Você precisa estar logado para ver sua lista de desejos.</p>
        <a href="<?php echo home_url('/login'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
            Fazer Login
        </a>
    </div>

    <!-- Wishlist Area -->
    <div id="wishlist-area" class="hidden">
        
        <!-- Loading -->
        <div id="loading-wishlist" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Carregando sua lista...</p>
        </div>

        <!-- Lista vazia -->
        <div id="empty-wishlist" class="hidden text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Sua lista está vazia</h3>
            <p class="text-gray-600 mb-6">Adicione produtos que você gostou para comprar depois.</p>
            <a href="<?php echo home_url('/produtos'); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
                Explorar Produtos
            </a>
        </div>

        <!-- Grid de produtos -->
        <div id="wishlist-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Produtos serão inseridos aqui via JavaScript -->
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = sessionStorage.getItem('customer_token');
    
    if (!token) {
        document.getElementById('not-authenticated').classList.remove('hidden');
        return;
    }
    
    document.getElementById('wishlist-area').classList.remove('hidden');
    loadWishlist();
});

function loadWishlist() {
    const token = sessionStorage.getItem('customer_token');
    
    fetch(window.RODUST_API_URL + '/api/wishlist', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-wishlist').classList.add('hidden');
        
        if (!data.wishlist || data.wishlist.length === 0) {
            document.getElementById('empty-wishlist').classList.remove('hidden');
            return;
        }
        
        renderWishlist(data.wishlist);
    })
    .catch(error => {
        console.error('Erro ao carregar wishlist:', error);
        document.getElementById('loading-wishlist').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <p class="text-red-700">Erro ao carregar lista de desejos. Tente novamente.</p>
            </div>
        `;
    });
}

function renderWishlist(items) {
    const grid = document.getElementById('wishlist-grid');
    const emptyMessage = document.getElementById('empty-wishlist');
    
    grid.innerHTML = '';
    
    // Se não houver itens, mostrar mensagem vazia
    if (!items || items.length === 0) {
        grid.classList.add('hidden');
        emptyMessage.classList.remove('hidden');
        return;
    }
    
    // Esconder mensagem vazia e mostrar grid
    emptyMessage.classList.add('hidden');
    
    items.forEach(item => {
        const product = item.product;
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow';
        card.innerHTML = `
            <div class="relative">
                <img src="${product.image || '/wp-content/themes/rodust/assets/images/placeholder-product.jpg'}" 
                     alt="${product.name}" 
                     class="w-full h-48 object-cover">
                <button 
                    onclick="removeFromWishlist(${product.id})"
                    class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">${product.name}</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl font-bold text-blue-600">
                        R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}
                    </span>
                    ${product.stock > 0 
                        ? `<span class="text-sm text-green-600">Em estoque</span>`
                        : `<span class="text-sm text-red-600">Indisponível</span>`
                    }
                </div>
                <div class="space-y-2">
                    ${product.stock > 0 
                        ? `<button 
                              onclick="addToCart(${product.id})"
                              class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                              🛒 Adicionar ao Carrinho
                           </button>`
                        : `<button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg cursor-not-allowed">
                              Indisponível
                           </button>`
                    }
                    <a href="/produto/${product.slug}" 
                       class="block text-center text-blue-600 hover:text-blue-800 text-sm">
                        Ver detalhes
                    </a>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
    
    grid.classList.remove('hidden');
}

function removeFromWishlist(productId) {
    if (!confirm('Remover este produto da lista de desejos?')) {
        return;
    }
    
    const token = sessionStorage.getItem('customer_token');
    
    fetch(window.RODUST_API_URL + '/api/wishlist/' + productId, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Produto removido:', data.message);
        loadWishlist(); // Recarregar lista
    })
    .catch(error => {
        console.error('Erro ao remover:', error);
        alert('Erro ao remover produto. Tente novamente.');
    });
}

function addToCart(productId) {
    // TODO: Implementar adicionar ao carrinho
    alert('Função de adicionar ao carrinho será implementada em breve!');
}
</script>

<?php get_footer(); ?>
