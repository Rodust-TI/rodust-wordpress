# 🎨 Rodust WordPress

Site e-commerce da Rodust desenvolvido em WordPress + Laravel (API).

## 📦 O que está neste repositório

- **Tema customizado**: `/wp-content/themes/rodust/`
- **Plugins customizados**:
  - `/wp-content/plugins/rodust-ecommerce/` - Integração com Laravel API
  - `/wp-content/plugins/rodust-carousel/` - Carrossel de produtos
  - `/wp-content/plugins/rodust-contact-form/` - Formulário de contato
  - `/wp-content/plugins/rodust-smtp/` - Configuração SMTP
  - `/wp-content/plugins/smart-menu-links/` - Menu inteligente

## 🚀 Como usar

### 1. Clone o repositório

```bash
git clone https://github.com/Rodust-TI/rodust-wordpress.git
cd rodust-wordpress
```

### 2. Configure wp-config.php

Copie o exemplo e configure:

```bash
cp wp-config-sample.php wp-config.php
```

Edite as credenciais do banco de dados:

```php
define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'mysql');
```

### 3. Configure a URL da API Laravel

No painel WordPress, vá em:

**Configurações → Rodust Ecommerce → URL da API**

```
http://localhost:8000/api
```

### 4. Importe o banco de dados

```bash
mysql -u root -p wordpress < backup.sql
```

## 🔧 Desenvolvimento

### Estrutura do Tema

```
wp-content/themes/rodust/
├── functions.php          # Funções do tema
├── header.php            # Cabeçalho
├── footer.php            # Rodapé
├── index.php             # Página inicial
├── page-*.php            # Templates de páginas
└── style.css             # Estilos
```

### Estrutura do Plugin Ecommerce

```
wp-content/plugins/rodust-ecommerce/
├── rodust-ecommerce.php  # Plugin principal
├── includes/
│   ├── class-api-client.php      # Cliente da API Laravel
│   ├── class-settings.php        # Painel de configurações
│   └── functions-urls.php        # Helper de URLs
└── README.md
```

## 📝 Variáveis de Ambiente

Adicione no `wp-config.php`:

```php
// URL da API Laravel
define('RODUST_API_URL', 'http://localhost:8000/api');

// SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-app');
```

## 🐳 Docker

Este WordPress roda em Docker junto com Laravel. Veja o repositório principal:
https://github.com/Rodust-TI/rodust-ecommerce

## 📚 Documentação

- [Laravel API](https://github.com/Rodust-TI/rodust-ecommerce)
- [WordPress Codex](https://codex.wordpress.org/)
- [Plugin Handbook](https://developer.wordpress.org/plugins/)

## 🤝 Contribuindo

1. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
2. Commit: `git commit -m 'Adiciona nova funcionalidade'`
3. Push: `git push origin feature/nova-funcionalidade`
4. Abra um Pull Request

## 📄 Licença

Proprietário - Rodust TI

---

**Desenvolvido por Rodust TI** 🚀
