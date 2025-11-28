# Smart Menu Links - Plugin WordPress

Plugin que permite usar **links inteligentes** nos menus do WordPress, eliminando a necessidade de digitar URLs completas.

## 🎯 Funcionalidades

### Links Especiais:
- `home` → Página inicial
- `blog` → Página do blog
- `contato` → Página de contato
- `sobre` → Página sobre

### Links por Slug:
- `produtos` → `/produtos/` (se a página existir)
- `servicos` → `/servicos/` (se a página existir)
- `qualquer-slug` → `/qualquer-slug/`

## 📝 Como Usar

### 1. No Admin do WordPress:
1. Vá em **Aparência > Menus**
2. Adicione um **Link personalizado**
3. No campo **URL**, digite apenas:
   - `home` (para página inicial)
   - `produtos` (para página produtos)
   - `contato` (para página contato)
   - etc.

### 2. Exemplos Práticos:

| Digite no campo URL | Resultado |
|-------------------|-----------|
| `home` | `https://seusite.com/` |
| `produtos` | `https://seusite.com/produtos/` |
| `contato` | `https://seusite.com/contato/` |
| `sobre` | `https://seusite.com/sobre/` |

## 🔧 Funciona com:
- ✅ Páginas WordPress
- ✅ Posts 
- ✅ Custom Post Types
- ✅ URLs âncora (#section)
- ✅ URLs completas (não modifica)

## 💡 Vantagens:
- **Mais rápido**: Não precisa copiar/colar URLs
- **Mais limpo**: Interface mais simples
- **Inteligente**: Busca automaticamente
- **Flexível**: Funciona com qualquer slug
- **Seguro**: Não quebra links existentes

## 🚀 Instalação:
1. Faça upload da pasta `smart-menu-links` para `/wp-content/plugins/`
2. Ative o plugin em **Plugins > Plugins instalados**
3. Pronto! Já funciona automaticamente nos menus

---

**Desenvolvido para o tema Rodust** 🎨