<?php
// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>⚙️ Configurações do Carousel</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('rodust_carousel_group'); ?>
        
        <table class="form-table">
            <tr>
                <th>Autoplay</th>
                <td>
                    <label>
                        <input type="checkbox" name="rodust_carousel_settings[autoplay]" value="1" 
                               <?php checked(1, $settings['autoplay'] ?? true); ?>>
                        Avançar automaticamente
                    </label>
                </td>
            </tr>
            <tr>
                <th>Velocidade (ms)</th>
                <td>
                    <input type="number" name="rodust_carousel_settings[autoplay_speed]" 
                           value="<?php echo esc_attr($settings['autoplay_speed'] ?? 5000); ?>" 
                           min="1000" max="10000" step="500">
                    <p class="description">Tempo entre slides (milissegundos)</p>
                </td>
            </tr>
            <tr>
                <th>Mostrar Dots</th>
                <td>
                    <label>
                        <input type="checkbox" name="rodust_carousel_settings[show_dots]" value="1" 
                               <?php checked(1, $settings['show_dots'] ?? true); ?>>
                        Pontos de navegação
                    </label>
                </td>
            </tr>
            <tr>
                <th>Mostrar Setas</th>
                <td>
                    <label>
                        <input type="checkbox" name="rodust_carousel_settings[show_arrows]" value="1" 
                               <?php checked(1, $settings['show_arrows'] ?? true); ?>>
                        Setas de navegação
                    </label>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Salvar Configurações'); ?>
    </form>
</div>
