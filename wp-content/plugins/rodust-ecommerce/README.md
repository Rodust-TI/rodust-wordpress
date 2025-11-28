# Rodust Ecommerce - Plugin WordPress

Plugin profissional de e-commerce integrado com API Laravel e Bling ERP.

## 📋 Índice

- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Arquitetura](#arquitetura)
- [Uso](#uso)
- [Desenvolvimento](#desenvolvimento)
- [FAQ](#faq)

---

## 🎯 Requisitos

- **WordPress:** 6.0 ou superior
- **PHP:** 8.0 ou superior
- **API Laravel:** Backend rodando (local ou produção)
- **Bling ERP:** Conta ativa (opcional para sincronização)

---

## 📦 Instalação

### Via WordPress Admin

1. Faça upload da pasta `rodust-ecommerce` para `/wp-content/plugins/`
2. Vá em **Plugins** → Localizar "Rodust Ecommerce"
3. Clique em **Ativar**

### Via WP-CLI

```bash
wp plugin activate rodust-ecommerce
```

---

## ⚙️ Configuração

### 1. Configurar URL da API Laravel

Após ativar o plugin, vá em:
```
WordPress Admin → Configurações → Rodust Ecommerce
```

**Configure:**
- **URL da API:** `http://localhost:8000/api` (desenvolvimento) ou `https://api.rodust.com.br/api` (produção)
- **Timeout:** 30 segundos (padrão)
- **Sincronização Automática:** Ativada
- **Intervalo de Sincronização:** 1 hora

### 2. Testar Conexão

Na página de configurações, clique em **"Testar Conexão"** para verificar se o WordPress consegue se comunicar com a API Laravel.

### 3. Estrutura de URLs

**Desenvolvimento (XAMPP + Docker):**
```
WordPress (XAMPP):  http://localhost         (porta 80)
Laravel (Docker):   http://localhost:8000    (porta 8000)
MySQL XAMPP:        localhost:3308
MySQL Docker:       localhost:3307
```

**Produção:**
```
WordPress:  https://rodust.com.br
Laravel:    https://api.rodust.com.br/api
```

---

## 🏗️ Arquitetura

### Fluxo de Dados

```
┌─────────────┐
│   Cliente   │ ← Navega no site
└──────┬──────┘
       │
       ↓
┌─────────────────────┐
│  WordPress (XAMPP)  │ ← Frontend (exibição)
│  localhost          │
└──────┬──────────────┘
       │ HTTP Request
       ↓
┌─────────────────────┐
│  Laravel (Docker)   │ ← Backend (API REST)
│  localhost:8000/api │
└──────┬──────────────┘
       │ Sincronização
       ↓
┌─────────────────────┐
│    Bling ERP        │ ← Sistema de origem
│  (OAuth2)           │
└─────────────────────┘
```

### Separação de Responsabilidades

| Camada | Responsabilidade | Tecnologia |
|--------|------------------|------------|
| **Frontend** | Exibir produtos, carrinho, checkout | WordPress + Plugin |
| **Backend** | API REST, lógica de negócio | Laravel + Sail |
| **ERP** | Estoque, pedidos, produtos | Bling API v3 |

### Por Que NÃO Colocar Token Bling no WordPress?

❌ **Nunca coloque credenciais de terceiros no WordPress!**

**Motivos:**
1. **Segurança:** Banco WordPress é alvo comum de ataques
2. **Arquitetura:** WordPress é camada de apresentação
3. **Manutenção:** Laravel centraliza integrações
4. **Auditoria:** Logs de API ficam no Laravel

✅ **Solução Correta:**
- Credenciais Bling ficam no `.env` do Laravel
- WordPress só precisa saber a URL da API Laravel
- Laravel expõe endpoints públicos para WordPress

---

## 📚 Uso

### Custom Post Type: Produtos

O plugin registra o CPT `rodust_product` com:

**Taxonomias:**
- `product_category` - Categorias (hierárquico)
- `product_tag` - Tags
- `product_brand` - Marcas
- `tool_type` - Tipos de Ferramenta

**Meta Fields:**
- `_sku` - Código do produto
- `_price` - Preço (R$)
- `_stock` - Quantidade em estoque
- `_laravel_id` - ID no banco Laravel
- `_synced_at` - Timestamp da última sincronização

### Shortcodes

#### Listar Produtos

```
[rodust_products per_page="12"]
```

**Atributos:**
- `per_page` - Produtos por página (padrão: 12)
- `category` - Filtrar por categoria
- `brand` - Filtrar por marca
- `search` - Busca por nome/descrição

#### Exibir Carrinho

```
[rodust_cart]
```

#### Formulário de Checkout

```
[rodust_checkout]
```

### Templates

Crie templates customizados no seu tema:

```
seu-tema/
├── single-rodust_product.php      # Página individual de produto
├── archive-rodust_product.php     # Listagem de produtos
└── taxonomy-product_category.php  # Arquivo de categoria
```

---

## 🛠️ Desenvolvimento

### Estrutura de Arquivos

```
rodust-ecommerce/
├── rodust-ecommerce.php              # Main plugin file
├── README.md                         # Esta documentação
├── includes/
│   ├── class-rodust-ecommerce.php    # Singleton principal
│   ├── class-api-client.php          # HTTP client genérico
│   ├── class-product-post-type.php   # Custom Post Type
│   ├── class-product-sync.php        # Sincronização WP ↔ Laravel
│   ├── class-cart-manager.php        # Gerenciamento de carrinho
│   ├── class-checkout-processor.php  # Processamento de pedidos
│   ├── class-shortcodes.php          # Shortcodes WordPress
│   ├── class-ajax-handlers.php       # Handlers AJAX
│   └── class-settings.php            # Gerenciamento de configurações
├── admin/
│   ├── class-admin-menu.php          # Menu do admin
│   └── class-admin-settings.php      # Página de configurações
└── assets/
    ├── css/
    │   ├── style.css                 # Estilos frontend
    │   └── admin.css                 # Estilos admin
    └── js/
        ├── script.js                 # JavaScript frontend
        └── admin.js                  # JavaScript admin
```

### Padrões de Código

- **PSR-4:** Autoloading de classes
- **Singleton:** Classes principais usam pattern Singleton
- **Hooks:** Usa ações e filtros do WordPress
- **SRP:** Uma responsabilidade por classe
- **Nomes:** Snake_case (padrão WordPress)

### API Endpoints Utilizados

O plugin consome os seguintes endpoints do Laravel:

```
GET    /api/products           # Listar produtos
GET    /api/products/{id}      # Ver produto
POST   /api/orders             # Criar pedido (checkout)
GET    /api/orders/{id}        # Ver pedido
```

---

## 🚀 Validação Bling (Desenvolvedores)

### Pré-requisitos

- Docker Desktop rodando
- Containers Laravel ativos
- Conta Bling com credenciais OAuth2

### Passo 1: Configurar Credenciais Bling no Laravel

**Local:** `M:\Websites\rodust.com.br\ecommerce\.env`

Adicione:
```env
BLING_CLIENT_ID=seu-client-id-aqui
BLING_CLIENT_SECRET=seu-client-secret-aqui
BLING_BASE_URL=https://api.bling.com.br/Api/v3
BLING_REDIRECT_URI=http://localhost:8000/bling/callback
```

### Passo 2: Obter Access Token

1. Acesse o painel Bling e gere um access token OAuth2
2. Copie o token

### Passo 3: Executar Comando de Validação

**Abrir PowerShell:**
```powershell
cd M:\Websites\rodust.com.br\ecommerce
```

**Executar validação:**
```powershell
docker compose exec laravel.test php artisan bling:validate --token=SEU_ACCESS_TOKEN_AQUI
```

### O Que o Comando Faz?

Executa os 5 passos do desafio de homologação Bling:

1. ✅ GET `/homologacao/produtos` - Obtém dados do produto
2. ✅ POST `/homologacao/produtos` - Cria produto
3. ✅ PUT `/homologacao/produtos/{id}` - Atualiza nome
4. ✅ PATCH `/homologacao/produtos/{id}/situacoes` - Altera situação
5. ✅ DELETE `/homologacao/produtos/{id}` - Remove produto

**Requisitos:**
- ⏱️ Tempo total máximo: 10 segundos
- 🔄 2 segundos entre cada requisição
- 🔐 Header `x-bling-homologacao` sequencial

### Exemplo de Saída

```
🚀 Iniciando validação Bling API v3...

📥 Passo 1: Obtendo dados do produto...
┌────────┬──────────────┐
│ Campo  │ Valor        │
├────────┼──────────────┤
│ Nome   │ Copo do Bling│
│ Preço  │ R$ 32,56     │
│ Código │ COD-4587     │
└────────┴──────────────┘
   Hash: iEL06HbaOdyrjw6F0cTk6z63ZOaI0Ezn0L43++ZjY/c=
✓ Passo 1: Produto obtido

📤 Passo 2: Criando produto...
   Hash: XyZ789AbC123...
✓ Passo 2: Produto criado (ID: 16842381880)

✏️  Passo 3: Atualizando produto...
   Hash: QwE456RtY...
✓ Passo 3: Produto atualizado

🔄 Passo 4: Alterando situação...
   Hash: ASD789FGH...
✓ Passo 4: Situação alterada

🗑️  Passo 5: Deletando produto...
   Hash: ZXC123VBN...
✓ Passo 5: Produto deletado

⏱️  Tempo total: 8.43s

🎉 Validação concluída com sucesso!
```

---

## ❓ FAQ

### Como testar o plugin sem Laravel rodando?

Você pode criar produtos manualmente no WordPress (`rodust_product`), mas a sincronização e checkout não funcionarão. Configure a URL da API para `http://localhost:8000/api` quando o Laravel estiver pronto.

### Posso usar este plugin com outro backend (não Laravel)?

**Sim!** O plugin é genérico e usa apenas REST API. Qualquer backend que implemente os endpoints documentados funcionará:
- `GET /api/products`
- `POST /api/orders`
- Etc.

### O que acontece se o Laravel estiver offline?

O plugin exibirá mensagem de erro na sincronização, mas produtos já sincronizados continuarão visíveis no WordPress (cache local).

### Preciso configurar CORS?

**Sim**, no Laravel adicione o domínio WordPress:

```php
// config/cors.php
'allowed_origins' => [
    'http://localhost',           // XAMPP local
    'https://rodust.com.br',     // Produção
],
```

### Como sincronizar produtos existentes do Bling?

No futuro, haverá um botão no admin: **"Importar do Bling"**. Por enquanto, crie produtos no WordPress e eles sincronizarão automaticamente.

### Onde ficam os logs de erro?

- **WordPress:** `wp-content/debug.log` (ative `WP_DEBUG_LOG`)
- **Laravel:** `storage/logs/laravel.log`

---

## 🔧 Troubleshooting

### Erro: "Não foi possível conectar à API"

**Verifique:**
1. Laravel está rodando? `docker compose ps`
2. URL da API está correta nas configurações?
3. Firewall bloqueando conexão?

**Teste manual:**
```bash
curl http://localhost:8000/api/products
```

### Produtos não aparecem no site

**Verifique:**
1. Produtos estão publicados (não rascunho)?
2. CPT `rodust_product` está registrado? (Plugins → Ativar novamente)
3. Rewrite rules: Configurações → Permalinks → Salvar

### Sincronização não funciona

**Verifique:**
1. Cron jobs WordPress ativos? `wp cron event list`
2. URL da API configurada?
3. Laravel acessível do WordPress?

---

## 📄 Licença

GPL v2 or later

---

## 🤝 Suporte

Para suporte técnico:
- **Email:** suporte@rodust.com.br
- **Documentação Laravel:** `M:\Websites\rodust.com.br\ecommerce\README.md`

---

**Desenvolvido por Rodust** 🚀
