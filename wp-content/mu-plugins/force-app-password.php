<?php
/**
 * Plugin Name: Force Application Password Auth
 * Description: Força autenticação via Application Password mesmo em HTTP para comunicação interna Docker
 */

// Força HTTPS para wp_is_application_passwords_available()
add_filter('wp_is_application_passwords_available', '__return_true', 999);

// Determina o usuário atual baseado em Application Password
add_filter('determine_current_user', function($user_id) {
    if ($user_id) {
        return $user_id;
    }
    
    // Verifica se há credenciais Basic Auth
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        return $user_id;
    }
    
    // Tenta autenticar com Application Password
    $user = wp_authenticate_application_password(null, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    
    if ($user && !is_wp_error($user)) {
        return $user->ID;
    }
    
    return $user_id;
}, 20);
