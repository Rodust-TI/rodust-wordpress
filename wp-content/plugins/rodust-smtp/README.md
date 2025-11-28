# Rodust SMTP Plugin

Plugin para configuração SMTP no WordPress, desenvolvido especificamente para o site Rodust.

## Recursos

- ✅ Configuração fácil via painel administrativo
- ✅ Suporte para Hostinger e Gmail SMTP
- ✅ Teste de e-mail integrado
- ✅ Interface amigável com presets
- ✅ Segurança e criptografia

## Configuração

### 1. Ativar o Plugin
1. Vá para **Plugins** > **Plugins Instalados**
2. Ative o **Rodust SMTP**

### 2. Configurar SMTP
1. Vá para **Configurações** > **SMTP Rodust**
2. Escolha uma das opções abaixo:

#### Hostinger SMTP (Recomendado)
```
Servidor SMTP: smtp.hostinger.com
Porta: 587
Segurança: TLS
Usuário: seu-email@rodust.com.br
Senha: senha-do-seu-email-hostinger
E-mail Remetente: contato@rodust.com.br
Nome Remetente: Rodust - Contato
```

#### Gmail SMTP
```
Servidor SMTP: smtp.gmail.com
Porta: 587
Segurança: TLS
Usuário: seu-email@gmail.com
Senha: app-password-do-gmail (não a senha normal)
E-mail Remetente: seu-email@gmail.com
Nome Remetente: Rodust - Contato
```

### 3. Testar Configuração
1. Na mesma página, role até **Teste de E-mail**
2. Digite seu e-mail
3. Clique em **Enviar E-mail Teste**
4. Verifique se recebeu o e-mail

## Configuração Gmail (Senha de App)

Para usar Gmail, você precisa criar uma "App Password":

1. Vá para [Google Account](https://myaccount.google.com/)
2. Clique em **Segurança** no menu lateral
3. Em "Como fazer login no Google", clique em **Verificação em duas etapas**
4. Role até "Senhas de app" e clique
5. Selecione **E-mail** e **Outros (nome personalizado)**
6. Digite "Rodust WordPress"
7. Use a senha gerada no plugin (não sua senha normal)

## Informações Necessárias

Para configurar, você precisa fornecer:

### Hostinger
- E-mail da conta Hostinger (ex: contato@rodust.com.br)
- Senha deste e-mail

### Gmail
- E-mail Gmail
- App Password (senha de aplicativo)

## Solução de Problemas

### E-mail não está sendo enviado
1. Verifique se todas as configurações estão corretas
2. Use o teste de e-mail para diagnosticar
3. Para Gmail, certifique-se de usar App Password
4. Verifique se a porta 587 não está bloqueada

### E-mail vai para SPAM
1. Use um e-mail do mesmo domínio do site como remetente
2. Configure SPF/DKIM no seu provedor de hospedagem
3. Hostinger SMTP geralmente tem melhor reputação

## Compatibilidade

- WordPress 5.0+
- PHP 7.4+
- Testado com Hostinger e Gmail

## Suporte

Para suporte, consulte a documentação do WordPress ou entre em contato com o desenvolvedor.