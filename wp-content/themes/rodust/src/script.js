// JavaScript personalizado do tema Rodust
document.addEventListener('DOMContentLoaded', function() {
    // Menu mobile toggle
    const mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Smooth scroll para links internos
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // ==========================================
    // USER MENU - Dropdown e Autenticação
    // ==========================================
    
    // Verificar se usuário está logado
    function isUserLoggedIn() {
        return sessionStorage.getItem('customer_token') !== null;
    }
    
    // Atualizar menus baseado no status de login
    function updateUserMenus() {
        const isLoggedIn = isUserLoggedIn();
        
        // Desktop
        const loggedInMenuDesktop = document.getElementById('user-logged-in-menu');
        const loggedOutMenuDesktop = document.getElementById('user-logged-out-menu');
        
        if (loggedInMenuDesktop && loggedOutMenuDesktop) {
            if (isLoggedIn) {
                loggedInMenuDesktop.classList.remove('hidden');
                loggedOutMenuDesktop.classList.add('hidden');
            } else {
                loggedInMenuDesktop.classList.add('hidden');
                loggedOutMenuDesktop.classList.remove('hidden');
            }
        }
        
        // Mobile
        const loggedInMenuMobile = document.getElementById('user-logged-in-menu-mobile');
        const loggedOutMenuMobile = document.getElementById('user-logged-out-menu-mobile');
        
        if (loggedInMenuMobile && loggedOutMenuMobile) {
            if (isLoggedIn) {
                loggedInMenuMobile.classList.remove('hidden');
                loggedOutMenuMobile.classList.add('hidden');
            } else {
                loggedInMenuMobile.classList.add('hidden');
                loggedOutMenuMobile.classList.remove('hidden');
            }
        }
    }
    
    // Toggle dropdown Desktop
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        
        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
    
    // Toggle dropdown Mobile
    const userMenuButtonMobile = document.getElementById('user-menu-button-mobile');
    const userDropdownMobile = document.getElementById('user-dropdown-mobile');
    
    if (userMenuButtonMobile && userDropdownMobile) {
        userMenuButtonMobile.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMobile.classList.toggle('hidden');
        });
        
        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!userMenuButtonMobile.contains(e.target) && !userDropdownMobile.contains(e.target)) {
                userDropdownMobile.classList.add('hidden');
            }
        });
    }
    
    // Logout Desktop
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Limpar sessão
            sessionStorage.removeItem('customer_token');
            sessionStorage.removeItem('customer_data');
            localStorage.removeItem('cart');
            
            // Atualizar badge do carrinho
            const cartBadge = document.getElementById('cart-count-badge');
            const cartBadgeMobile = document.getElementById('cart-count-badge-mobile');
            if (cartBadge) cartBadge.classList.add('hidden');
            if (cartBadgeMobile) cartBadgeMobile.classList.add('hidden');
            
            // Redirecionar para home
            window.location.href = '/';
        });
    }
    
    // Logout Mobile
    const logoutButtonMobile = document.getElementById('logout-button-mobile');
    if (logoutButtonMobile) {
        logoutButtonMobile.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Limpar sessão
            sessionStorage.removeItem('customer_token');
            sessionStorage.removeItem('customer_data');
            localStorage.removeItem('cart');
            
            // Atualizar badge do carrinho
            const cartBadge = document.getElementById('cart-count-badge');
            const cartBadgeMobile = document.getElementById('cart-count-badge-mobile');
            if (cartBadge) cartBadge.classList.add('hidden');
            if (cartBadgeMobile) cartBadgeMobile.classList.add('hidden');
            
            // Redirecionar para home
            window.location.href = '/';
        });
    }
    
    // Atualizar menus ao carregar a página
    updateUserMenus();
    
    // ==========================================
    // SEARCH MODAL - Busca Global
    // ==========================================
    
    const searchToggle = document.getElementById('search-toggle-button');
    const searchModal = document.getElementById('search-modal');
    const searchInput = document.getElementById('global-search-input');
    const closeSearchBtn = document.getElementById('close-search-modal');
    const searchBackdrop = document.getElementById('search-backdrop');
    const searchResults = document.getElementById('search-results');
    
    let searchTimeout;
    
    if (searchToggle && searchModal) {
        // Abrir modal
        searchToggle.addEventListener('click', function() {
            searchModal.classList.remove('hidden');
            searchInput.focus();
        });
        
        // Fechar modal
        if (closeSearchBtn) {
            closeSearchBtn.addEventListener('click', closeSearch);
        }
        if (searchBackdrop) {
            searchBackdrop.addEventListener('click', closeSearch);
        }
        
        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
                closeSearch();
            }
        });
        
        // Buscar enquanto digita
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 3) {
                    searchResults.innerHTML = '<p class="text-gray-500 text-sm">Digite pelo menos 3 caracteres...</p>';
                    return;
                }
                
                searchResults.innerHTML = '<p class="text-gray-500 text-sm">Buscando...</p>';
                
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 500);
            });
        }
    }
    
    function closeSearch() {
        if (searchModal) {
            searchModal.classList.add('hidden');
            searchInput.value = '';
            searchResults.innerHTML = '<p class="text-gray-500 text-sm">Digite para buscar produtos...</p>';
        }
    }
    
    function performSearch(query) {
        fetch(window.RODUST_API_URL + '/api/products?search=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (!data.products || data.products.length === 0) {
                    searchResults.innerHTML = '<p class="text-gray-500 text-sm">Nenhum produto encontrado.</p>';
                    return;
                }
                
                let html = '<div class="space-y-3">';
                data.products.slice(0, 5).forEach(product => {
                    html += `
                        <a href="/produto/${product.slug}" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <img src="${product.image || '/placeholder.jpg'}" alt="${product.name}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">${product.name}</h4>
                                <p class="text-blue-600 font-bold">R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}</p>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
                
                if (data.products.length > 5) {
                    html += `<div class="mt-4 pt-4 border-t text-center">
                        <a href="/produtos?search=${encodeURIComponent(query)}" class="text-blue-600 hover:text-blue-800">
                            Ver todos os ${data.products.length} resultados
                        </a>
                    </div>`;
                }
                
                searchResults.innerHTML = html;
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                searchResults.innerHTML = '<p class="text-red-500 text-sm">Erro ao buscar. Tente novamente.</p>';
            });
    }
    
    // ==========================================
    // WISHLIST - Contador
    // ==========================================
    
    function updateWishlistCount() {
        const token = sessionStorage.getItem('customer_token');
        if (!token) return;
        
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
            const count = data.wishlist ? data.wishlist.length : 0;
            const badge = document.getElementById('wishlist-count-badge');
            
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        })
        .catch(error => console.error('Erro ao carregar wishlist count:', error));
    }
    
    // Atualizar contador de wishlist ao carregar
    updateWishlistCount();
});