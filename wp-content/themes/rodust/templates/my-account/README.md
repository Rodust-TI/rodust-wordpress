# 📁 Arquitetura Modular - Minha Conta

## 🎯 Objetivo

Refatorar `page-minha-conta.php` (1260+ linhas) em uma estrutura modular seguindo princípios **SOLID** e **SRP (Single Responsibility Principle)**.

---

## 📂 Estrutura de Diretórios

```
wp-content/themes/rodust/
│
├── page-minha-conta-refactored.php        (150 linhas - estrutura principal)
│
├── templates/
│   └── my-account/
│       ├── partials/
│       │   ├── header.php                 (cabeçalho da página)
│       │   └── navigation.php             (navegação de abas)
│       │
│       ├── tabs/
│       │   ├── personal-data.php          (aba dados pessoais)
│       │   ├── addresses.php              (aba endereços)
│       │   ├── orders.php                 (aba pedidos)
│       │   └── wishlist.php               (aba lista de desejos)
│       │
│       └── modals/
│           ├── address-form.php           (modal formulário endereço)
│           └── order-details.php          (modal detalhes pedido)
│
└── assets/
    └── js/
        └── my-account/
            ├── main.js                    (núcleo - auth, tabs, helpers)
            ├── personal-data.js           (lógica dados pessoais)
            ├── addresses.js               (lógica endereços)
            ├── orders.js                  (lógica pedidos)
            └── wishlist.js                (lógica wishlist)
```

---

## 🏗️ Arquitetura

### **1. PHP - Templates**

#### **Arquivo Principal** (`page-minha-conta-refactored.php`)
```php
<?php
get_header();

// Importar partials
get_template_part('templates/my-account/partials/header');
get_template_part('templates/my-account/partials/navigation');

// Importar tabs
get_template_part('templates/my-account/tabs/personal-data');
get_template_part('templates/my-account/tabs/addresses');
get_template_part('templates/my-account/tabs/orders');
get_template_part('templates/my-account/tabs/wishlist');

// Importar modals
get_template_part('templates/my-account/modals/address-form');
get_template_part('templates/my-account/modals/order-details');

get_footer();
?>
```

**Vantagens:**
- ✅ Apenas **150 linhas** no arquivo principal
- ✅ Fácil manutenção de cada seção
- ✅ Reutilização de componentes
- ✅ Testes isolados

---

### **2. JavaScript - Módulos**

#### **Módulo Principal** (`main.js`)
```javascript
const MyAccount = (function($) {
    // Autenticação
    // Navegação de abas
    // Funções compartilhadas (formatters, toast, etc)
    // Sistema de eventos
    
    return {
        init,
        getToken,
        getCustomerData,
        switchTab,
        logout,
        showToast,
        formatCPF,
        formatCNPJ,
        formatZipcode
    };
})(jQuery);
```

#### **Módulos Especializados**
- `personal-data.js` - Edição de dados pessoais
- `addresses.js` - CRUD de endereços
- `orders.js` - Listagem e detalhes de pedidos
- `wishlist.js` - Gestão de lista de desejos

**Comunicação entre módulos:**
```javascript
// main.js dispara eventos
$(document).trigger('myaccount:loaded', [customerData]);
$(document).trigger('myaccount:tab-changed', [tab]);

// orders.js escuta eventos
$(document).on('myaccount:tab-changed', function(e, tab) {
    if (tab === 'pedidos' && !ordersLoaded) {
        loadOrders();
    }
});
```

---

## 🔧 Como Usar

### **1. Criar Página no WordPress**

1. Painel Admin → Páginas → Adicionar Nova
2. Título: "Minha Conta"
3. Template: Selecionar "Minha Conta (Refatorado)"
4. Publicar

### **2. Adicionar Nova Aba**

**PHP** (`templates/my-account/tabs/new-tab.php`):
```php
<div id="tab-nova-aba" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2>Título da Nova Aba</h2>
        <!-- Conteúdo -->
    </div>
</div>
```

**Incluir no main** (`page-minha-conta-refactored.php`):
```php
get_template_part('templates/my-account/tabs/new-tab');
```

**JavaScript** (`assets/js/my-account/new-tab.js`):
```javascript
const MyAccountNewTab = (function($) {
    function init() {
        $(document).on('myaccount:tab-changed', function(e, tab) {
            if (tab === 'nova-aba') {
                // Lógica ao abrir a aba
            }
        });
    }
    
    return { init };
})(jQuery);

jQuery(document).ready(() => MyAccountNewTab.init());
```

**Enqueue script** (`page-minha-conta-refactored.php`):
```php
wp_enqueue_script('my-account-new-tab', 
    get_template_directory_uri() . '/assets/js/my-account/new-tab.js', 
    array('jquery', 'my-account-main'), '1.0', true);
```

---

## 📊 Comparação

| Aspecto | Antes | Depois |
|---------|-------|--------|
| **Linhas** | 1260 linhas | 150 linhas (main) |
| **Manutenção** | Difícil | Fácil |
| **Testabilidade** | Baixa | Alta |
| **Reutilização** | Nenhuma | Total |
| **Colaboração** | Difícil | Fácil |
| **Performance** | Mesma | Mesma |

---

## 🎨 Princípios Aplicados

### **1. SRP (Single Responsibility Principle)**
Cada arquivo tem **uma única responsabilidade**:
- `orders.js` → Apenas pedidos
- `addresses.js` → Apenas endereços

### **2. DRY (Don't Repeat Yourself)**
Funções compartilhadas em `main.js`:
- `formatCPF()`, `formatCNPJ()`, `showToast()`

### **3. Separation of Concerns**
- **PHP** → Estrutura e HTML
- **JavaScript** → Lógica e interação
- **CSS** → Tailwind classes inline

### **4. Event-Driven Architecture**
Comunicação via eventos customizados:
```javascript
$(document).trigger('myaccount:loaded');
$(document).on('myaccount:loaded', handler);
```

---

## 🚀 Próximos Passos

1. ✅ Migrar aba "Dados Pessoais"
2. ✅ Migrar aba "Endereços"
3. ✅ Migrar aba "Pedidos" (exemplo completo criado)
4. ⏳ Migrar aba "Wishlist"
5. ⏳ Adicionar testes unitários
6. ⏳ Documentar API de cada módulo

---

## 📝 Convenções

### **Nomes de Arquivos**
- PHP: `kebab-case.php`
- JS: `kebab-case.js`
- IDs: `kebab-case`
- Classes CSS: Tailwind

### **Estrutura de Módulo JS**
```javascript
const ModuleName = (function($) {
    // Private variables
    let variable = null;
    
    // Private functions
    function privateFunction() { }
    
    // Public API
    return {
        publicFunction
    };
})(jQuery);
```

---

## 🤝 Colaboração

Com essa estrutura, múltiplos desenvolvedores podem trabalhar simultaneamente:
- Dev 1 → `personal-data.js`
- Dev 2 → `addresses.js`
- Dev 3 → `orders.js`

**Sem conflitos de merge!**

---

## 📚 Referências

- [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Module Pattern (JavaScript)](https://addyosmani.com/resources/essentialjsdesignpatterns/book/#modulepatternjavascript)

---

**Criado em:** 28/11/2025  
**Autor:** Rodust Development Team
