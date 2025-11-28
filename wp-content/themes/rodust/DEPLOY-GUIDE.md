# 🚀 Guia Completo de Deploy - Rodust

## ❌ **O que NÃO subir para produção:**

### **Arquivos de Desenvolvimento:**
```
node_modules/          (muito pesado - até 200MB+)
src/                   (arquivos fonte)
.git/                  (controle de versão)
package-lock.json      (dependências específicas)
gulpfile.js           (ferramenta de build)
tailwind.config.js    (configuração de desenvolvimento)
```

### **Arquivos Temporários:**
```
.DS_Store             (macOS)
Thumbs.db             (Windows)
*.log                 (logs)
*.tmp                 (temporários)
```

---

## ✅ **O que SUBIR para produção:**

### **Tema WordPress:**
```
rodust/
├── style.css         ← Compilado pelo Gulp
├── assets/
│   ├── css/style.css ← Minificado para produção
│   └── js/script.js  ← Minificado para produção
├── functions.php
├── index.php
├── header.php
├── footer.php
├── home.php
├── page-contato.php
├── archive.php
├── front-page.php
├── screenshot.png
└── README.md
```

### **Plugins:**
```
rodust-smtp/
rodust-carousel/
rodust-contact-form/
smart-menu-links/
```

---

## 🎯 **Processo Ideal de Deploy:**

### **1. Preparar Build Local:**
```powershell
# No diretório do tema
npm run build
```

### **2. Criar Pacote para Upload:**
Copiar apenas arquivos necessários:
- Tema compilado (sem node_modules)
- Plugins desenvolvidos
- Imagens/assets

### **3. Upload via FTP/Gerenciador:**
- Subir apenas arquivos de produção
- Não subir `node_modules`, `src/`, etc.

### **4. Configurar no Servidor:**
- Ativar plugins
- Configurar SMTP (pode usar as mesmas credenciais)
- Testar funcionalidades

---

## 🚀 **Solução Automática:**

Use o script `prepare-deploy.bat` que cria uma pasta `deploy/` com apenas os arquivos necessários para produção!

**Execute:** 
M:\Websites\rodust.com.br\wordpress\wp-content\themes\rodust> npm run build
Depois:
Clique duplo no `prepare-deploy.bat` ou execute `./prepare-deploy.bat`