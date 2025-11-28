# 🛠️ Rodust Dev Tools

Must-Use Plugin para ferramentas de desenvolvimento e manutenção do WordPress.

## 📦 O que é?

Este plugin carrega automaticamente no WordPress (pasta `mu-plugins`) e fornece um **painel administrativo** com ferramentas úteis para desenvolvimento.

## ✨ Funcionalidades

### 1. **Limpar Produtos** 🗑️
- Lista todos os produtos do tipo `rodust_product`
- Permite deletar todos de uma vez (útil para testes)
- Exibe informações: ID, SKU, Bling ID, Preço, Estoque

### 2. **Flush Rewrite Rules** 🔄
- Atualiza regras de reescrita de URL
- Lista todos os post types personalizados
- Útil após alterações em permalinks ou registro de novos post types

### 3. **Gerenciar Plugins** 🔌
- Visualizar todos os plugins instalados
- Ativar/desativar plugins com um clique
- Status visual (ativo/inativo)

### 4. **Testar API Laravel** 🔗
- Testa conexão com a API Laravel
- Exibe resposta da API
- Útil para debug de integração

## 🔒 Segurança

**O plugin só carrega em ambiente de desenvolvimento:**
- `localhost`
- `localhost:8080`
- `localhost:8443`
- `127.0.0.1`

Em produção, o plugin **não carrega** e o menu não aparece.

## 📍 Como Acessar

Após instalar, acesse o WordPress Admin:

**Menu:** `Dev Tools` (ícone de ferramentas no menu lateral)

## 📂 Estrutura

```
wp-content/mu-plugins/
├── rodust-dev-tools.php         # Plugin principal (carrega automaticamente)
└── rodust-dev-tools/            # (opcional) Assets futuros
    ├── css/
    ├── js/
    └── includes/
```

## 🚀 Instalação

1. Copie `rodust-dev-tools.php` para `wp-content/mu-plugins/`
2. Acesse o WordPress Admin
3. O menu "Dev Tools" aparecerá automaticamente (apenas em localhost)

## ⚙️ Configuração

### URL da API Laravel

O Dev Tools **lê automaticamente** a URL da API configurada no plugin `rodust-ecommerce`:

**Prioridade de configuração:**
1. **Configuração do Plugin** → `Rodust Ecommerce → Configurações → API URL`
2. **Função Helper** → `rodust_plugin_get_api_url()` (se disponível)
3. **Constante wp-config.php** → `RODUST_API_URL` (fallback)

**Como configurar:**
1. Acesse: `Rodust Ecommerce → Configurações`
2. Preencha o campo "URL da API Laravel"
3. Exemplo: `http://localhost:8000/api`
4. Salve as alterações

✅ **Benefício:** Configuração centralizada em um único lugar!

## 🔧 Desenvolvimento

### Adicionar Nova Ferramenta

1. Adicione submenu em `add_action('admin_menu', ...)`
2. Crie função callback para renderizar a página
3. Use `check_admin_referer()` para segurança em formulários

Exemplo:

```php
add_submenu_page(
    'rodust-dev-tools',
    'Minha Ferramenta',
    'Ferramenta',
    'manage_options',
    'rodust-minha-ferramenta',
    'rodust_minha_ferramenta_page'
);

function rodust_minha_ferramenta_page() {
    ?>
    <div class="wrap">
        <h1>Minha Ferramenta</h1>
        <!-- Seu HTML aqui -->
    </div>
    <?php
}
```

## 📝 Substituição de Scripts Soltos

Este plugin **substitui** os seguintes scripts que estavam soltos na raiz:

| Script Antigo | Nova Localização |
|---------------|------------------|
| `limpar-produtos.php` | Dev Tools → Limpar Produtos |
| `ativar-plugin-e-flush.php` | Dev Tools → Plugins + Flush Rewrite |
| `flush-rewrite.php` | Dev Tools → Flush Rewrite |
| `test-app-password.php` | *(removido - usar Postman/curl)* |

## 🎯 Benefícios

✅ **Organizado** - Tudo em um painel admin  
✅ **Seguro** - Só em desenvolvimento  
✅ **Visual** - Interface nativa do WordPress  
✅ **Extensível** - Fácil adicionar novas ferramentas  
✅ **Profissional** - Sem scripts soltos na raiz  

## 🔮 Futuras Melhorias

- [ ] Ferramenta de importação/exportação de dados
- [ ] Monitor de sincronização Laravel ↔ WordPress
- [ ] Visualizador de logs em tempo real
- [ ] Ferramenta de debug de requests API
- [ ] Gerador de dados fake para testes

## 📞 Suporte

Desenvolvido por **Rodust TI**

---

**Nota:** Mantenha este plugin **apenas em desenvolvimento**. Não faça deploy para produção.
