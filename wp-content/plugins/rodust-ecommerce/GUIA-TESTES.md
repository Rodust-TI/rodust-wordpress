# 🧪 Guia de Testes do Plugin (Sem API Keys)

## ⚠️ Problemas Identificados e Soluções

### 1. ✅ Erro "Session cannot be started after headers have already been sent"
**Causa:** WordPress já enviou headers antes do `session_start()`

**Solução aplicada:** 
- Movido `session_start()` para hook `init` do WordPress
- Verifica se headers já foram enviados antes de iniciar sessão
- Arquivo corrigido: `class-cart-manager.php`

### 2. ✅ Página mostra "Arquivo" em vez de "Produtos"
**Causa:** É o comportamento normal do WordPress para archives

**Como personalizar:**
- Edite a página no WordPress Admin
- Adicione um título personalizado
- Ou use o shortcode `[rodust_products]` em vez do archive

### 3. ✅ Não tem produtos para testar
**Solução:** Script automático criado!

---

## 🚀 Passo a Passo para Testar SEM API Keys

### PASSO 1: Criar Produtos de Teste

**Opção A - Script Automático (Recomendado):**

1. Acesse no navegador:
```
http://localhost/wp-content/plugins/rodust-ecommerce/criar-produto-teste.php
```

2. O script vai criar 6 produtos automaticamente:
   - ✅ Parafusadeira Elétrica DEWALT DCD771 (R$ 599,90)
   - ✅ Martelo Carpinteiro Stanley 25mm (R$ 89,90)
   - ✅ Jogo de Chaves Phillips 6 Peças (R$ 45,50)
   - ✅ Trena Digital Laser Bosch GLM 50 (R$ 349,00)
   - ✅ Serra Circular Makita 7.1/4" (R$ 679,90)
   - ✅ Nível a Laser Triplo Feixe (R$ 199,90)

**Opção B - Manualmente no WordPress:**

1. WordPress Admin → Produtos → Adicionar novo
2. Preencher:
   - **Título:** Nome do produto
   - **Conteúdo:** Descrição
   - **Imagem destacada:** Foto do produto
3. Na sidebar, preencher campos customizados:
   - **Preço:** 99.90 (sem R$)
   - **Estoque:** 10
   - **SKU:** PROD-001
   - **Laravel ID:** 1 (qualquer número)

---

### PASSO 2: Verificar se Produtos Foram Criados

1. **Ver no Admin:**
```
http://localhost/wp-admin/edit.php?post_type=product
```

2. **Ver na página:**
```
http://localhost/produtos
```

Deve mostrar grid com os produtos criados.

---

### PASSO 3: Testar Carrinho (Modo Básico - Sem Frete/Pagamento)

#### 3.1. Adicionar ao Carrinho
1. Vá em `/produtos`
2. Clique "Adicionar ao Carrinho" em qualquer produto
3. Deve aparecer notificação verde de sucesso
4. Contador do carrinho deve atualizar (se tiver no menu)

#### 3.2. Ver Carrinho
1. Vá em `/carrinho`
2. Deve mostrar tabela com produto adicionado
3. Testar:
   - ➕ Aumentar quantidade
   - ➖ Diminuir quantidade  
   - ❌ Remover produto
   - 🗑️ Limpar carrinho

#### 3.3. Calculadora de Frete (vai dar erro - normal)
⚠️ **Esperado:** Erro ao calcular frete
- **Por quê?** Não tem token do Melhor Envio configurado
- **Solução:** Testar depois de configurar API (não é necessário agora)

#### 3.4. Checkout (modo limitado)
1. Vá em `/checkout`
2. Deve mostrar:
   - ✅ Formulário de dados pessoais
   - ✅ Endereço de entrega
   - ✅ Resumo do pedido (sidebar)
   - ⚠️ Opções de pagamento (não funcionam ainda)

**Ao clicar "Finalizar Compra":**
- ⚠️ **Esperado:** Erro - "Não foi possível processar o pedido"
- **Por quê?** Precisa de:
  1. Token Mercado Pago (para criar pagamento)
  2. Melhor Envio configurado (para frete)

---

## 🔍 O Que Funciona AGORA (Sem API Keys)

✅ **Funciona 100%:**
- Listar produtos
- Adicionar ao carrinho
- Atualizar quantidade
- Remover produtos
- Limpar carrinho
- Ver contador de itens
- Navegação entre páginas
- Layout responsivo
- Notificações (toast)

⚠️ **Funciona Parcialmente:**
- Checkout (formulário OK, mas não finaliza compra)
- Calculadora de frete (interface OK, mas não calcula)

❌ **NÃO Funciona (precisa APIs):**
- Calcular frete real
- Processar pagamento
- Criar pedido no Laravel
- Sincronizar com Bling

---

## 📋 Checklist de Testes Básicos

### Teste 1: Listagem de Produtos ✅
- [ ] Acessar `/produtos`
- [ ] Ver grid de produtos
- [ ] Ver imagens, preços, nomes
- [ ] Ver botão "Adicionar ao Carrinho"
- [ ] Ver badge de estoque ("Em estoque", "Últimas unidades")

### Teste 2: Adicionar ao Carrinho ✅
- [ ] Clicar em "Adicionar ao Carrinho"
- [ ] Ver notificação verde de sucesso
- [ ] Contador atualizar (se visível)
- [ ] Adicionar mesmo produto novamente (deve somar quantidade)

### Teste 3: Carrinho ✅
- [ ] Acessar `/carrinho`
- [ ] Ver tabela com produtos
- [ ] Clicar ➕ (quantidade aumenta)
- [ ] Clicar ➖ (quantidade diminui)
- [ ] Clicar ❌ (produto removido)
- [ ] Ver subtotal atualizar automaticamente

### Teste 4: Página do Produto ✅
- [ ] Clicar em um produto
- [ ] Ver galeria de imagens (se tiver múltiplas)
- [ ] Ver descrição completa
- [ ] Ver especificações (aba "Especificações")
- [ ] Escolher quantidade
- [ ] Adicionar ao carrinho

### Teste 5: Responsividade ✅
- [ ] Redimensionar navegador
- [ ] Grid de produtos ajusta colunas
- [ ] Carrinho responsivo em mobile
- [ ] Checkout vira 1 coluna em mobile

---

## 🐛 Erros Esperados (Normal!)

### ❌ Erro ao Calcular Frete
```
Erro ao calcular frete. Verifique as configurações.
```
**Normal!** Precisa configurar Melhor Envio.

### ❌ Erro ao Finalizar Compra
```
Não foi possível processar o pedido. Tente novamente.
```
**Normal!** Precisa configurar:
1. Mercado Pago (pagamento)
2. Melhor Envio (frete)
3. Laravel API rodando

### ⚠️ Warning de Session
Se ainda aparecer:
```
Warning: session_start(): Session cannot be started...
```

**Solução:**
1. Limpar cache do navegador (Ctrl+Shift+Delete)
2. Desativar e reativar o plugin
3. Verificar se não tem output antes do `<?php` nos arquivos

---

## 🎯 Próximos Passos (Quando Quiser Testar Completo)

### 1. Configurar Melhor Envio (Frete)
**Criar conta:** https://melhorenvio.com.br

**Obter token:**
1. Login no Melhor Envio
2. Configurações → Token & Chaves
3. Gerar Token de API
4. Copiar token

**Configurar no WordPress:**
1. Admin → Configurações → Rodust Ecommerce
2. Seção "Melhor Envio (Frete)"
3. Colar token
4. Informar CEP de origem
5. Marcar "Modo Sandbox" (para testes)
6. Salvar

### 2. Configurar Mercado Pago (Pagamento)
**Criar conta:** https://mercadopago.com.br

**Obter credenciais:**
1. Login no Mercado Pago
2. Acessar: https://www.mercadopago.com.br/developers/panel/app
3. Criar aplicativo
4. Copiar credenciais de **TESTE** (sandbox)

**Configurar no WordPress:**
1. Admin → Configurações → Rodust Ecommerce
2. Seção "Mercado Pago (Pagamento)"
3. Colar Access Token e Public Key
4. Marcar "Modo Sandbox"
5. Salvar

### 3. Testar Fluxo Completo
Depois de configurar tudo:
1. Adicionar produto ao carrinho ✅
2. Calcular frete (vai funcionar!) ✅
3. Escolher transportadora ✅
4. Finalizar compra ✅
5. Redirecionar para Mercado Pago ✅
6. Efetuar pagamento teste ✅

---

## 📞 Troubleshooting Rápido

**P: Carrinho não atualiza?**
R: Abrir console (F12) e ver erros JavaScript

**P: Produtos não aparecem?**
R: Verificar se tem produtos publicados em Admin → Produtos

**P: Imagens não carregam?**
R: Verificar URL das imagens ou usar script automático

**P: Página em branco?**
R: Verificar erros PHP em `wp-content/debug.log` (ativar WP_DEBUG)

**P: Notificações não aparecem?**
R: Verificar se JavaScript está carregando (F12 → Network)

---

**Última atualização:** 14/11/2025
**Status:** ✅ Funcional para testes básicos (sem APIs externas)
