# 🛠️ Helpers - Rodust Ecommerce

## Visão Geral

As classes Helpers centralizam funções utilitárias para evitar duplicação de código. Existem duas versões:
- **PHP**: `includes/class-helpers.php` - Para uso no backend WordPress
- **JavaScript**: `assets/js/helpers.js` - Para uso no frontend

---

## 📦 Helpers PHP

### Localização
`wp-content/plugins/rodust-ecommerce/includes/class-helpers.php`

### Carregamento
Registrado automaticamente em `rodust-ecommerce.php`:
```php
require_once RODUST_ECOMMERCE_PATH . 'includes/class-helpers.php';
```

### Métodos Disponíveis

#### Formatação de Documentos

##### `format_cpf($cpf)`
Formata CPF: `12345678901` → `123.456.789-01`

```php
$formatted = Rodust_Helpers::format_cpf('12345678901');
// Output: 123.456.789-01
```

##### `format_cnpj($cnpj)`
Formata CNPJ: `12345678000190` → `12.345.678/0001-90`

```php
$formatted = Rodust_Helpers::format_cnpj('12345678000190');
// Output: 12.345.678/0001-90
```

##### `format_document($document)`
Formata CPF ou CNPJ automaticamente (detecta tamanho)

```php
$formatted = Rodust_Helpers::format_document('12345678901'); // CPF
// Output: 123.456.789-01

$formatted = Rodust_Helpers::format_document('12345678000190'); // CNPJ
// Output: 12.345.678/0001-90
```

#### Validação de Documentos

##### `validate_cpf($cpf)`
Valida CPF (com ou sem formatação)

```php
$valid = Rodust_Helpers::validate_cpf('123.456.789-01');
// Output: true ou false
```

##### `validate_cnpj($cnpj)`
Valida CNPJ (com ou sem formatação)

```php
$valid = Rodust_Helpers::validate_cnpj('12.345.678/0001-90');
// Output: true ou false
```

##### `validate_document($document)`
Valida CPF ou CNPJ automaticamente

```php
$valid = Rodust_Helpers::validate_document('123.456.789-01'); // Valida como CPF
$valid = Rodust_Helpers::validate_document('12.345.678/0001-90'); // Valida como CNPJ
```

#### Formatação de Preços

##### `format_price($value, $show_currency = true)`
Formata preço: `1234.56` → `R$ 1.234,56`

```php
$formatted = Rodust_Helpers::format_price(1234.56);
// Output: R$ 1.234,56

$formatted = Rodust_Helpers::format_price(1234.56, false);
// Output: 1.234,56 (sem R$)
```

**Substituiu:**
```php
// ANTES (duplicado em vários arquivos):
'R$ ' . number_format($price, 2, ',', '.')

// DEPOIS:
Rodust_Helpers::format_price($price)
```

#### Telefone

##### `sanitize_phone($phone)`
Remove caracteres não numéricos

```php
$sanitized = Rodust_Helpers::sanitize_phone('(11) 98765-4321');
// Output: 11987654321
```

##### `format_phone($phone)`
Formata telefone: `11987654321` → `(11) 98765-4321`

```php
$formatted = Rodust_Helpers::format_phone('11987654321');
// Output: (11) 98765-4321
```

#### CEP

##### `sanitize_postal_code($postal_code)`
Remove caracteres não numéricos

```php
$sanitized = Rodust_Helpers::sanitize_postal_code('13400-710');
// Output: 13400710
```

##### `format_postal_code($postal_code)`
Formata CEP: `13400710` → `13400-710`

```php
$formatted = Rodust_Helpers::format_postal_code('13400710');
// Output: 13400-710
```

#### Outras Utilidades

##### `validate_email($email)`
Valida email

```php
$valid = Rodust_Helpers::validate_email('contato@rodust.com.br');
// Output: true
```

##### `truncate($text, $length = 100, $suffix = '...')`
Trunca texto com reticências

```php
$truncated = Rodust_Helpers::truncate('Texto muito longo...', 20);
// Output: Texto muito longo...
```

##### `slugify($text)`
Gera slug amigável para URL

```php
$slug = Rodust_Helpers::slugify('Parafusadeira Elétrica 500W');
// Output: parafusadeira-eletrica-500w
```

##### `escape_html($text)`
Escapa HTML para segurança

```php
$safe = Rodust_Helpers::escape_html('<script>alert("xss")</script>');
// Output: &lt;script&gt;alert("xss")&lt;/script&gt;
```

##### `get_first_words($text, $words = 1)`
Obtém primeiras palavras (útil para nome/sobrenome)

```php
$first = Rodust_Helpers::get_first_words('João da Silva', 1);
// Output: João
```

##### `get_last_words($text, $words = 1)`
Obtém últimas palavras

```php
$last = Rodust_Helpers::get_last_words('João da Silva', 1);
// Output: Silva
```

---

## 🌐 Helpers JavaScript

### Localização
`wp-content/plugins/rodust-ecommerce/assets/js/helpers.js`

### Carregamento
Enfileirado automaticamente em `class-rodust-ecommerce.php`:
```php
wp_enqueue_script(
    'rodust-helpers',
    RODUST_ECOMMERCE_URL . 'assets/js/helpers.js',
    [],
    RODUST_ECOMMERCE_VERSION,
    true
);
```

### Namespace
Todas as funções estão no namespace `RodustHelpers`:
```javascript
RodustHelpers.formatPrice(1234.56);
```

### Métodos Disponíveis

#### `formatPrice(value, showCurrency = true)`
Formata preço: `1234.56` → `R$ 1.234,56`

```javascript
const formatted = RodustHelpers.formatPrice(1234.56);
// Output: "R$ 1.234,56"

const formatted = RodustHelpers.formatPrice(1234.56, false);
// Output: "1.234,56"
```

**Substituiu:**
```javascript
// ANTES (duplicado em vários arquivos):
'R$ ' + price.toFixed(2).replace('.', ',')

// DEPOIS:
RodustHelpers.formatPrice(price)
```

#### `sanitizeDocument(document)`
Remove caracteres não numéricos de CPF/CNPJ

```javascript
const sanitized = RodustHelpers.sanitizeDocument('123.456.789-01');
// Output: "12345678901"
```

#### `formatCPF(cpf)`
Formata CPF

```javascript
const formatted = RodustHelpers.formatCPF('12345678901');
// Output: "123.456.789-01"
```

#### `formatCNPJ(cnpj)`
Formata CNPJ

```javascript
const formatted = RodustHelpers.formatCNPJ('12345678000190');
// Output: "12.345.678/0001-90"
```

#### `formatDocument(document)`
Formata CPF ou CNPJ automaticamente

```javascript
const formatted = RodustHelpers.formatDocument('12345678901');
// Output: "123.456.789-01" (detectou CPF)
```

#### `sanitizePhone(phone)` / `formatPhone(phone)`
Sanitiza e formata telefone

```javascript
const sanitized = RodustHelpers.sanitizePhone('(11) 98765-4321');
// Output: "11987654321"

const formatted = RodustHelpers.formatPhone('11987654321');
// Output: "(11) 98765-4321"
```

#### `sanitizePostalCode(postalCode)` / `formatPostalCode(postalCode)`
Sanitiza e formata CEP

```javascript
const sanitized = RodustHelpers.sanitizePostalCode('13400-710');
// Output: "13400710"

const formatted = RodustHelpers.formatPostalCode('13400710');
// Output: "13400-710"
```

#### `escapeHtml(text)`
Escapa HTML para segurança

```javascript
const safe = RodustHelpers.escapeHtml('<script>alert("xss")</script>');
// Output: "&lt;script&gt;alert("xss")&lt;/script&gt;"
```

#### `debounce(func, wait)`
Limita execução de função (útil para busca em tempo real)

```javascript
const searchProducts = RodustHelpers.debounce(function(query) {
    console.log('Searching for:', query);
}, 300);

// Executa apenas uma vez após parar de digitar por 300ms
searchInput.addEventListener('input', (e) => {
    searchProducts(e.target.value);
});
```

---

## 📊 Impacto da Refatoração

### Arquivos Alterados (PHP)
- ✅ `includes/class-shipping-calculator.php` - 2 substituições
- ✅ `templates/single-product.php` - 2 substituições
- ✅ `templates/checkout/order-summary.php` - 3 substituições
- ✅ `templates/cart.php` - 4 substituições
- ✅ `templates/archive-products.php` - 1 substituição
- ✅ `includes/class-cart-manager.php` - 1 substituição

**Total:** 13 ocorrências de `number_format()` substituídas

### Arquivos com Padrão Duplicado (JavaScript)
- ✅ `assets/js/payment.js` - 5 ocorrências substituídas
- ✅ `assets/js/script.js` - 5 ocorrências substituídas
- ✅ `assets/js/rodust-ecommerce.js` - 4 ocorrências substituídas
- ✅ `assets/js/checkout-shipping.js` - 3 ocorrências substituídas
- ⚠️ `assets/js/product-admin.js` - 5 ocorrências mantidas (inputs de formulário)

**Total:** 17 ocorrências substituídas por `RodustHelpers.formatPrice()`

### Benefícios
1. **Manutenibilidade**: Mudança de formato em 1 único lugar
2. **Consistência**: Todos os preços formatados da mesma forma
3. **Testabilidade**: Funções isoladas e testáveis
4. **Reutilização**: Evita copiar/colar código
5. **DRY**: Don't Repeat Yourself aplicado

---

## 🧪 Testes Recomendados

### Teste Manual (PHP)
1. Acessar página de produto individual
2. Verificar formatação de preços (R$ X.XXX,XX)
3. Adicionar produto ao carrinho
4. Verificar formatação no carrinho
5. Ir para checkout
6. Verificar resumo do pedido
7. Calcular frete
8. Verificar formatação do valor do frete

### Teste Manual (JavaScript)
1. Abrir console do navegador
2. Testar funções:
```javascript
RodustHelpers.formatPrice(1234.56)
RodustHelpers.formatCPF('12345678901')
RodustHelpers.formatPhone('11987654321')
```

### Teste de Regressão
- [ ] Listagem de produtos: preços exibidos corretamente
- [ ] Produto individual: preço principal formatado
- [ ] Carrinho: subtotal, total e frete formatados
- [ ] Checkout: resumo do pedido com valores corretos
- [ ] Produtos relacionados: preços formatados

---

## ✅ Fase 5 - Status Final

### Completo (100%)
- ✅ Classe PHP criada (20+ métodos)
- ✅ Helpers JavaScript criados (namespace RodustHelpers)
- ✅ Documentação escrita (HELPERS.md)
- ✅ Substituições PHP realizadas (13 ocorrências)
- ✅ Substituições JavaScript realizadas (17 ocorrências)
- ✅ Testes de sintaxe OK
- ✅ Git commits realizados

### Estatísticas Finais
- **Total de código duplicado eliminado:** 30 ocorrências
- **PHP:** 13 substituições em 6 arquivos
- **JavaScript:** 17 substituições em 4 arquivos
- **Linhas de código adicionadas:** +677 (helpers)
- **Linhas de código removidas:** ~60 (duplicações)
- **Impacto:** Manutenibilidade ⬆️⬆️⬆️ | Consistência ⬆️⬆️⬆️ | Testabilidade ⬆️⬆️⬆️

---

**Criado em:** 2025-11-28  
**Última atualização:** 2025-11-28  
**Status:** ✅ COMPLETO

