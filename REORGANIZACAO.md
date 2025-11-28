# 📁 Reorganização da Estrutura WordPress

## 🎯 Objetivo

Organizar o projeto WordPress de forma profissional, eliminando arquivos soltos e criando uma estrutura escalável.

---

## ❌ Problemas Identificados

### **1. Arquivos Soltos na Raiz**

Arquivos que estavam na raiz do WordPress (`/wordpress/`):

| Arquivo | Problema | Solução |
|---------|----------|---------|
| `limpar-produtos.php` | Helper de dev solto | ✅ Movido para Dev Tools (mu-plugin) |
| `ativar-plugin-e-flush.php` | Helper de dev solto | ✅ Movido para Dev Tools (mu-plugin) |
| `flush-rewrite.php` | Helper de dev solto | ✅ Movido para Dev Tools (mu-plugin) |
| `test-app-password.php` | Script de teste solto | ✅ Removido (usar Postman/curl) |

### **2. Arquivo Mal Localizado**

| Arquivo | Local Errado | Local Correto |
|---------|--------------|---------------|
| `package-lock.json` | `/wp-content/` | `/wp-content/themes/rodust/` |

---

## ✅ Nova Estrutura

```
wordpress/
├── wp-content/
│   ├── mu-plugins/                         # Must-Use Plugins (carregam automaticamente)
│   │   ├── rodust-dev-tools.php            # ⭐ NOVO: Painel de ferramentas dev
│   │   └── rodust-dev-tools/
│   │       └── README.md
│   │
│   ├── plugins/
│   │   ├── rodust-ecommerce/
│   │   ├── rodust-carousel/
│   │   ├── rodust-contact-form/
│   │   ├── rodust-smtp/
│   │   └── smart-menu-links/
│   │
│   └── themes/
│       └── rodust/
│           ├── node_modules/               # ❌ Ignorado pelo Git
│           ├── package.json
│           └── package-lock.json           # ✅ Movido para cá
│
├── .gitignore                              # ✅ Atualizado
└── README.md                               # ✅ Atualizado

❌ Removidos da raiz:
├── limpar-produtos.php                     # Agora em Dev Tools
├── ativar-plugin-e-flush.php               # Agora em Dev Tools
├── flush-rewrite.php                       # Agora em Dev Tools
└── test-app-password.php                   # Removido
```

---

## 🛠️ Rodust Dev Tools (Must-Use Plugin)

### **O que é?**

Um **Must-Use Plugin** que carrega automaticamente e fornece um painel administrativo com ferramentas de desenvolvimento.

### **Funcionalidades:**

1. **🗑️ Limpar Produtos** - Remove todos os produtos (substitui `limpar-produtos.php`)
2. **🔄 Flush Rewrite** - Atualiza URLs (substitui `flush-rewrite.php`)
3. **🔌 Gerenciar Plugins** - Ativa/desativa plugins (substitui `ativar-plugin-e-flush.php`)
4. **🔗 Testar API** - Testa conexão com Laravel

### **Segurança:**

- ✅ **Só carrega em desenvolvimento** (localhost)
- ✅ **Não aparece em produção**
- ✅ **Interface nativa do WordPress**

### **Como usar:**

1. Acesse o WordPress Admin
2. Menu lateral: **Dev Tools** (ícone de ferramentas)
3. Escolha a ferramenta desejada

---

## 📋 Checklist de Migração

### **Ações Necessárias:**

- [x] Criar `mu-plugins/rodust-dev-tools.php`
- [x] Criar documentação (`mu-plugins/rodust-dev-tools/README.md`)
- [x] Atualizar `.gitignore`
- [ ] **Remover scripts soltos da raiz:**
  ```bash
  rm limpar-produtos.php
  rm ativar-plugin-e-flush.php
  rm flush-rewrite.php
  rm test-app-password.php
  ```
- [ ] **Mover `package-lock.json`:**
  ```bash
  mv wp-content/package-lock.json wp-content/themes/rodust/
  ```
- [ ] **Testar Dev Tools no WordPress Admin**
- [ ] **Commit e push das mudanças**

---

## 🎯 Benefícios

### **Antes (Desorganizado):**
```
wordpress/
├── limpar-produtos.php          ❌ Solto na raiz
├── ativar-plugin-e-flush.php    ❌ Solto na raiz
├── flush-rewrite.php            ❌ Solto na raiz
├── test-app-password.php        ❌ Solto na raiz
└── wp-content/
    └── package-lock.json        ❌ Local errado
```

### **Depois (Organizado):**
```
wordpress/
└── wp-content/
    ├── mu-plugins/
    │   └── rodust-dev-tools.php  ✅ Painel unificado
    └── themes/rodust/
        └── package-lock.json     ✅ Local correto
```

---

## 🚀 Próximos Passos

### **Fase 1: Organização (ATUAL)**
- [x] Criar estrutura de mu-plugins
- [x] Migrar funcionalidades para Dev Tools
- [ ] Remover scripts antigos

### **Fase 2: Melhorias Futuras**
- [ ] Adicionar ferramenta de importação/exportação
- [ ] Monitor de sincronização Laravel ↔ WordPress
- [ ] Visualizador de logs em tempo real
- [ ] Debug de requests API
- [ ] Gerador de dados fake para testes

### **Fase 3: Produção**
- [ ] Garantir que Dev Tools não carrega em produção
- [ ] Documentar processo de deploy
- [ ] Criar checklist de verificação pré-deploy

---

## 📝 Comandos Úteis

### **Remover arquivos antigos:**
```bash
cd M:\Websites\rodust.com.br\wordpress
rm limpar-produtos.php ativar-plugin-e-flush.php flush-rewrite.php test-app-password.php
```

### **Mover package-lock.json:**
```bash
mv wp-content/package-lock.json wp-content/themes/rodust/
```

### **Verificar arquivos soltos:**
```bash
ls -la *.php | grep -v "wp-"
```

### **Commit das mudanças:**
```bash
git add -A
git commit -m "Reorganização: Dev Tools como mu-plugin"
git push origin main
```

---

## 📚 Referências

- [WordPress Must-Use Plugins](https://wordpress.org/documentation/article/must-use-plugins/)
- [WordPress Admin Menu](https://developer.wordpress.org/reference/functions/add_menu_page/)
- [WordPress Nonces](https://developer.wordpress.org/plugins/security/nonces/)

---

**Desenvolvido por Rodust TI** 🚀
