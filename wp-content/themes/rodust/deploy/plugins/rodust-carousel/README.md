# 🎠 Rodust Carousel - Plugin WordPress

Plugin completo de carousel responsivo com **painel de administração** intuitivo para gerenciar slides, imagens e links.

## 🎯 Funcionalidades

### ✨ Interface Admin:
- **📊 Painel dedicado** no menu WordPress
- **🖼️ Upload de imagens** via Media Library
- **📝 Formulários organizados** para cada slide
- **🔀 Arrastar e soltar** para reordenar slides
- **⚙️ Configurações globais** (autoplay, velocidade, etc.)
- **💾 Salvamento automático** via AJAX

### 🎨 Carousel Frontend:
- **📱 Totalmente responsivo** (desktop, tablet, mobile)
- **👆 Touch/swipe** para dispositivos móveis
- **⌨️ Navegação por teclado** (setas esquerda/direita)
- **🎯 Setas de navegação** personalizáveis
- **⚪ Dots indicator** com slide atual
- **⏯️ Autoplay configurável** com pause no hover
- **🔗 Links inteligentes** (integração com Smart Menu Links)

## 📐 Especificações

### **Altura Padrão:** 300px
### **Responsive Breakpoints:**
- **Desktop:** 300px altura
- **Tablet:** 250px altura  
- **Mobile:** 200px altura

## 🚀 Como Usar

### 1. **Ativação:**
1. Ative o plugin em **Plugins > Plugins instalados**
2. Aparecerá o menu **"Carousel"** no admin

### 2. **Configuração:**
1. Vá em **Carousel** no menu admin
2. Configure as **opções globais**:
   - ✅ Autoplay (liga/desliga)
   - ⏱️ Velocidade (1000-10000ms)
   - ⚪ Mostrar dots
   - ➡️ Mostrar setas

### 3. **Adicionar Slides:**
1. Clique **"➕ Adicionar Novo Slide"**
2. Preencha os campos:
   - **📝 Título** (obrigatório)
   - **🖼️ Imagem** (obrigatório - 300px altura ideal)
   - **🔗 Link** (opcional - use links inteligentes!)
   - **📄 Texto do Link** (ex: "Saiba Mais")
   - **📋 Descrição** (opcional)
3. Clique **"💾 Salvar"**

### 4. **Reordenar Slides:**
- **🔀 Arraste e solte** usando o ícone ≡
- Ordem é salva automaticamente

## 📋 Implementação

### **Shortcode (em posts/páginas):**
```
[rodust_carousel]
```

### **Função PHP (no tema):**
```php
<?php echo rodust_carousel(); ?>
```

### **Com parâmetros personalizados:**
```php
<?php echo rodust_carousel(array(
    'height' => '400px',
    'class' => 'minha-classe-custom'
)); ?>
```

## 🔗 Links Inteligentes

**Integração com Smart Menu Links:**
- `home` → Página inicial
- `produtos` → /produtos/
- `contato` → /contato/
- `sobre` → /sobre/
- URLs normais funcionam também

## 🎨 Customização CSS

### **Classes disponíveis:**
```css
.rodust-carousel { } /* Container principal */
.carousel-slide { } /* Cada slide */
.carousel-slide-content { } /* Área de texto */
.carousel-slide-title { } /* Título do slide */
.carousel-slide-description { } /* Descrição */
.carousel-slide-link { } /* Botão de link */
.carousel-arrows { } /* Setas navegação */
.carousel-dots { } /* Dots navegação */
```

## 📱 Responsividade

**Breakpoints automáticos:**
- **768px+:** Layout desktop completo
- **481-767px:** Layout tablet otimizado  
- **<480px:** Layout mobile compacto

## ⚡ Performance

- **🚀 CSS/JS minificados** para produção
- **📦 Carregamento lazy** das imagens
- **🎯 Scripts carregados** apenas quando necessário
- **💾 Cache de configurações**

## 🛠️ Requisitos Técnicos

- **WordPress:** 5.0+
- **PHP:** 7.4+
- **jQuery:** Incluído no WordPress
- **Browsers:** Todos modernos + IE11

---

**🎨 Desenvolvido para o tema Rodust** com foco em **usabilidade** e **performance**!