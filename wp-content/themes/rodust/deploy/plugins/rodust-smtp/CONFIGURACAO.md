# 📧 Guia de Configuração de E-mails - Rodust

## Estratégia Recomendada de Dois E-mails

### 🎯 **Objetivo:** 
Separar e-mails de **recebimento** dos e-mails de **envio automático** para melhor organização e segurança.

---

## 📮 **Configuração dos E-mails**

### **1. contato@rodust.com.br** (E-mail de Recebimento)
- **Função:** Receber mensagens dos clientes
- **Configuração:** Caixa de entrada normal
- **Quem usa:** Você para ler e responder mensagens
- **Configurar em:** Painel da Hostinger como e-mail normal

### **2. noreply@rodust.com.br** (E-mail de Envio)
- **Função:** Enviar notificações automáticas do site
- **Configuração:** Apenas para SMTP (não precisa ler)
- **Quem usa:** Sistema WordPress para enviar
- **Configurar em:** Plugin SMTP do WordPress

---

## 🔧 **Passos para Configuração**

### **Passo 1: Criar os E-mails na Hostinger**
1. Acesse o painel da Hostinger
2. Vá em **E-mails**
3. Crie dois e-mails:
   - `contato@rodust.com.br` (com senha forte)
   - `noreply@rodust.com.br` (com senha forte)

### **Passo 2: Configurar WordPress**
1. **Ativar Plugin SMTP:**
   - WordPress Admin → Plugins → Ativar "Rodust SMTP"

2. **Configurar SMTP:**
   - Configurações → SMTP Rodust
   - Preencher com dados do `noreply@rodust.com.br`:

```
Servidor SMTP: smtp.hostinger.com
Porta: 587
Segurança: TLS
Usuário: noreply@rodust.com.br
Senha: [senha-do-noreply]
E-mail Remetente: noreply@rodust.com.br
Nome Remetente: Rodust - Sistema
```

3. **Configurar Destinatário:**
   - O formulário enviará para: `contato@rodust.com.br`
   - Mas será enviado através de: `noreply@rodust.com.br`

### **Passo 3: Testar**
1. Use o "Teste de E-mail" no plugin
2. Digite `contato@rodust.com.br` como destinatário
3. Verifique se recebeu o e-mail

---

## 📊 **Como Funcionará**

### **Fluxo do E-mail:**
```
Cliente preenche formulário
       ↓
WordPress usa noreply@rodust.com.br para enviar
       ↓
E-mail chega em contato@rodust.com.br
       ↓
Você lê e responde normalmente
```

### **Cabeçalhos do E-mail:**
- **De (From):** Rodust - Sistema <noreply@rodust.com.br>
- **Para (To):** contato@rodust.com.br
- **Responder para (Reply-To):** Nome do Cliente <email-do-cliente>

### **Vantagem:**
- Cliente pode responder o e-mail normalmente
- A resposta vai direto para você
- Organização perfeita entre automático vs manual

---

## 🔐 **Informações Necessárias**

Para completar a configuração, preciso que você forneça:

1. **Senha do noreply@rodust.com.br** (depois de criá-lo na Hostinger)
2. **Confirmação se o contato@rodust.com.br já está criado**

---

## 🆘 **Troubleshooting**

### **E-mail não chega:**
- Verificar se as senhas estão corretas
- Confirmar que a porta 587 não está bloqueada
- Testar com o sistema de teste do plugin

### **E-mail vai para SPAM:**
- Usar sempre o domínio rodust.com.br
- Evitar palavras como "oferta", "promoção" no assunto
- Hostinger geralmente tem boa reputação

### **Erro de autenticação:**
- Verificar usuário e senha
- Confirmar que o e-mail foi criado na Hostinger
- Tentar recriar a senha do e-mail

