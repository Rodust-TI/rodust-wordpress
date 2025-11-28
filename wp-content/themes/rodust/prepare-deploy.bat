@echo off
echo 🚀 Preparando Deploy da Rodust...
echo.

REM Navega para o diretório do tema
cd /d "M:\Websites\rodust.com.br\wordpress\wp-content\themes\rodust"

echo ⚡ Gerando build de produção...
call npm run build

echo.
echo 📦 Criando pasta de deploy...
if exist "deploy" rmdir /s /q "deploy"
mkdir "deploy"
mkdir "deploy\themes"
mkdir "deploy\plugins"

echo.
echo 📁 Copiando tema (arquivos de produção)...
mkdir "deploy\themes\rodust"

REM Copia arquivos essenciais do tema (sem node_modules e src)
xcopy "*.php" "deploy\themes\rodust\" /Y
xcopy "*.css" "deploy\themes\rodust\" /Y
xcopy "*.png" "deploy\themes\rodust\" /Y
xcopy "*.md" "deploy\themes\rodust\" /Y
xcopy "*.html" "deploy\themes\rodust\" /Y
xcopy "assets\*" "deploy\themes\rodust\assets\" /S /Y
xcopy "inc\*" "deploy\themes\rodust\inc\" /S /Y

echo.
echo 🔌 Copiando plugins...
xcopy "..\..\plugins\rodust-smtp" "deploy\plugins\rodust-smtp\" /S /Y /I
xcopy "..\..\plugins\rodust-carousel" "deploy\plugins\rodust-carousel\" /S /Y /I
xcopy "..\..\plugins\rodust-contact-form" "deploy\plugins\rodust-contact-form\" /S /Y /I
xcopy "..\..\plugins\smart-menu-links" "deploy\plugins\smart-menu-links\" /S /Y /I

echo.
echo ✅ Deploy preparado com sucesso!
echo.
echo 📋 Próximos passos:
echo 1. Acesse a pasta: deploy\
echo 2. Suba o conteúdo via FTP/Gerenciador da Hostinger
echo 3. Ative os plugins no WordPress
echo 4. Configure SMTP na produção
echo.
echo 🎯 Pasta deploy criada em: %cd%\deploy\
echo.
pause