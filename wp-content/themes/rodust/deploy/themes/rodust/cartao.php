<?php /*
Template Name: Cartão de Visita Rodust
*/ ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartão de Visita | Rodust</title>
    <meta property="og:title" content="Rodust - Ferramentas e Parafusos">
    <meta property="og:description" content="Sua nova loja de Ferramentas em Piracicaba. R. Noel Rosa, 65, Higienópolis, Piracicaba - SP. WhatsApp: +55 19 99201-5005">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/image/LogoBranco.png">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#181a1b">
    <style>
        body {
            background: #181a1b;
            color: #fff;
            font-family: 'Outfit', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #23272b;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.25);
            padding: 2.5rem 2rem;
            max-width: 370px;
            width: 100%;
            text-align: center;
        }
        .logo {
            width: 120px;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 2rem;
            margin: 0 0 0.5rem 0;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .desc {
            color: #b0b0b0;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .info {
            margin-bottom: 1.5rem;
        }
        .info p {
            margin: 0.3rem 0;
            font-size: 1.05rem;
        }
        .social {
            margin-bottom: 1.5rem;
        }
        .social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            color: #fff;
            background: #262b2f;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.3rem;
            transition: background 0.2s;
            text-decoration: none;
        }
        .social a:hover {
            background: #e1306c;
        }
        .whatsapp {
            display: inline-block;
            background: #25d366;
            color: #fff;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 1.2rem;
            transition: background 0.2s;
        }
        .whatsapp:hover {
            background: #1ebe5d;
        }
        .address {
            color: #b0b0b0;
            font-size: 0.98rem;
            margin-bottom: 0.5rem;
        }
        .email {
            color: #b0b0b0;
            font-size: 0.98rem;
            margin-bottom: 0.5rem;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="card">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/LogoBranco.png" alt="Logo Rodust" class="logo">
        <!-- <h5>Rodust</h5> -->
        <div class="desc">Sua nova loja de Ferramentas em Piracicaba</div>
        <div class="info">
            <a class="whatsapp" href="https://wa.me/5519992015005" target="_blank">WhatsApp: +55 19 99201-5005</a>
            <br>
            <p class="email">contato@rodust.com.br</p>
            <br>
            <p class="address">R. Noel Rosa, 65<br>Higienópolis, Piracicaba - SP<br>CEP: 13424-371</p>
        </div>
        <div class="social"> Acesse nosso Instagram:
            <a href="https://www.instagram.com/rodustferramentaseparafusos/" target="_blank" title="Instagram" aria-label="Instagram">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
            </a>
        </div>
    </div>
</body>
</html>