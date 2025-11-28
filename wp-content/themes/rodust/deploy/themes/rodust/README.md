# 🚀 Tema Rodust - WordPress

Tema WordPress moderno desenvolvido com **Tailwind CSS** e **PHP tradicional** (sem Gutenberg blocks).

## 📋 Tecnologias Utilizadas

- **WordPress** - CMS
- **Tailwind CSS** - Framework CSS utilitário
- **Gulp** - Automação de tarefas
- **PostCSS** - Processamento de CSS
- **JavaScript Vanilla** - Funcionalidades interativas
- **PHP** - Desenvolvimento tradicional WordPress

## 🎯 Recursos

✅ **Responsivo completo** (mobile-first)  
✅ **Menu com dropdown** e Nav Walker para Tailwind  
✅ **Sistema de build automatizado** com Gulp  
✅ **CSS otimizado** com purge automático  
✅ **Performance otimizada**  
✅ **SEO ready**  
✅ **Acessibilidade (WCAG)**  
✅ **Suporte a logo customizado**  

## 🛠️ Instalação e Configuração

### 1. Pré-requisitos

- **Node.js** (versão 16 ou superior)
- **npm** ou **yarn**
- **WordPress** instalado e funcionando

### 2. Instalação das Dependências

**No terminal do VS Code, navegue até o diretório do tema:**

```bash
cd M:\Websites\rodust.com.br\wordpress\wp-content\themes\rodust
```

**Instale as dependências:**

```bash
npm install
```

### 3. Comandos de Desenvolvimento

**Desenvolvimento (com watch automático):**
```bash
# Opção 1: usando npm script
npm run dev

# Opção 2: usando gulp diretamente  
gulp

# Opção 3: apenas watch
gulp watch
```

**Build para Produção:**
```bash
# CSS e JS minificados
npm run build
# ou
gulp build
```

**Comandos individuais:**
```bash
# Apenas CSS
gulp css

# Apenas JavaScript
gulp js
```

### 4. Estrutura de Arquivos

```
rodust/
├── 📄 README.md (este arquivo)
├── 📄 style.css (informações do tema)
├── 📄 functions.php (configurações PHP)
├── 📄 index.php (template principal)
├── 📄 header.php (cabeçalho)
├── 📄 footer.php (rodapé)
├── 📄 single.php (post individual)
├── 📄 archive.php (arquivo/categoria)
├── 📄 gulpfile.js (automação)
├── 📄 package.json (dependências)
├── 📄 tailwind.config.js (configuração Tailwind)
├── 📁 inc/
│   └── class-tailwind-nav-walker.php
├── 📁 src/ (arquivos fonte)
│   ├── style.css (Tailwind CSS)
│   └── script.js (JavaScript)
└── 📁 assets/ (arquivos compilados)
    ├── css/style.css
    └── js/script.js
```

## 🎨 Configuração do WordPress

### 1. Ativar o Tema
1. Acesse **Aparência > Temas** no admin
2. Ative o tema **Rodust**

### 2. Configurar Menu
1. Vá em **Aparência > Menus**
2. Crie um novo menu
3. Defina a localização como **"Menu Principal"**

### 3. Configurar Logo
1. Acesse **Aparência > Personalizar > Identidade do Site**
2. Faça upload do logo
3. O logo será exibido automaticamente no header

## 📐 Especificações do Logo

### Formato Recomendado: **WebP**
- ✅ **Melhor compressão** (30-50% menor que PNG)
- ✅ **Suporte a transparência**
- ✅ **Qualidade superior**
- ✅ **Suportado por todos browsers modernos**

### Tamanhos Recomendados:

**Logo Principal (Header):**
- **Largura:** 200px - 300px
- **Altura:** 50px - 80px
- **Proporção:** 3:1 ou 4:1 (horizontal)
- **Formato:** `.webp` com fundo transparente

**Fallback (PNG):**
- Mesmas dimensões em `.png` para browsers antigos

### Exemplo de preparação:
```
logo-rodust.webp (250x70px) - arquivo principal
logo-rodust.png (250x70px) - fallback
```

## 🎛️ Personalização

### Cores do Tema
Edite o arquivo `tailwind.config.js`:

```javascript
theme: {
  extend: {
    colors: {
      'rodust-primary': '#1e40af',    // Azul principal
      'rodust-secondary': '#64748b',  // Cinza secundário  
      'rodust-accent': '#f59e0b',     // Amarelo destaque
    }
  }
}
```

### Fontes
As fontes estão configuradas no `header.php` e `tailwind.config.js`. 
Fonte atual: **Inter** (Google Fonts)

## 🔧 Desenvolvimento

### Workflow Recomendado:

1. **Abra o terminal no diretório do tema:**
   ```bash
   cd M:\Websites\rodust.com.br\wordpress\wp-content\themes\rodust
   ```

2. **Inicie o modo desenvolvimento:**
   ```bash
   npm run dev
   ```

3. **Edite os arquivos:**
   - CSS: `src/style.css`
   - JS: `src/script.js`
   - PHP: qualquer arquivo `.php`

4. **O Gulp irá:**
   - ✅ Compilar Tailwind CSS automaticamente
   - ✅ Processar JavaScript
   - ✅ Fazer reload quando houver mudanças
   - ✅ Otimizar para produção (com `build`)

## 📱 Suporte a Dispositivos

- **Desktop:** 1280px+
- **Tablet:** 768px - 1279px  
- **Mobile:** 320px - 767px

## 🎯 Menu e Navegação

O tema inclui **Nav Walker personalizado** para Tailwind CSS:
- Menu responsivo com hamburger mobile
- Dropdown automático para submenus
- Estados ativos e hover
- Totalmente acessível (ARIA)

## 🚀 Deploy/Produção

Antes de fazer deploy:

```bash
# Build otimizado
npm run build

# Arquivos gerados em assets/ estarão minificados
```

## 📞 Suporte

Para dúvidas sobre o desenvolvimento do tema:
- Documentação Tailwind CSS: https://tailwindcss.com/docs
- Documentação WordPress: https://developer.wordpress.org/

---

**Desenvolvido com ❤️ para Rodust**