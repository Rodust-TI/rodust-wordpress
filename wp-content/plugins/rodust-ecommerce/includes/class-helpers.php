<?php
/**
 * Helper Functions Class
 * 
 * Centraliza funções utilitárias para evitar duplicação de código
 *
 * @package RodustEcommerce
 */

defined('ABSPATH') || exit;

class Rodust_Helpers {
    
    /**
     * Formatar CPF: 12345678901 -> 123.456.789-01
     * 
     * @param string $cpf CPF sem formatação
     * @return string CPF formatado
     */
    public static function format_cpf($cpf) {
        if (empty($cpf)) {
            return '';
        }
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) !== 11) {
            return $cpf;
        }
        
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
    
    /**
     * Formatar CNPJ: 12345678000190 -> 12.345.678/0001-90
     * 
     * @param string $cnpj CNPJ sem formatação
     * @return string CNPJ formatado
     */
    public static function format_cnpj($cnpj) {
        if (empty($cnpj)) {
            return '';
        }
        
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }
        
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
    
    /**
     * Formatar CPF ou CNPJ automaticamente
     * 
     * @param string $document Documento sem formatação
     * @return string Documento formatado
     */
    public static function format_document($document) {
        if (empty($document)) {
            return '';
        }
        
        $document = preg_replace('/[^0-9]/', '', $document);
        
        if (strlen($document) === 11) {
            return self::format_cpf($document);
        } elseif (strlen($document) === 14) {
            return self::format_cnpj($document);
        }
        
        return $document;
    }
    
    /**
     * Validar CPF
     * 
     * @param string $cpf CPF com ou sem formatação
     * @return bool True se válido
     */
    public static function validate_cpf($cpf) {
        if (empty($cpf)) {
            return false;
        }
        
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }
        
        // Verifica se não são todos iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        // Validação do primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cpf[9]) !== $digit1) {
            return false;
        }
        
        // Validação do segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        return intval($cpf[10]) === $digit2;
    }
    
    /**
     * Validar CNPJ
     * 
     * @param string $cnpj CNPJ com ou sem formatação
     * @return bool True se válido
     */
    public static function validate_cnpj($cnpj) {
        if (empty($cnpj)) {
            return false;
        }
        
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verifica se tem 14 dígitos
        if (strlen($cnpj) !== 14) {
            return false;
        }
        
        // Verifica se não são todos iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }
        
        // Validação do primeiro dígito verificador
        $sum = 0;
        $multipliers = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($cnpj[$i]) * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cnpj[12]) !== $digit1) {
            return false;
        }
        
        // Validação do segundo dígito verificador
        $sum = 0;
        $multipliers = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += intval($cnpj[$i]) * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        return intval($cnpj[13]) === $digit2;
    }
    
    /**
     * Validar CPF ou CNPJ automaticamente
     * 
     * @param string $document Documento com ou sem formatação
     * @return bool True se válido
     */
    public static function validate_document($document) {
        if (empty($document)) {
            return false;
        }
        
        $document = preg_replace('/[^0-9]/', '', $document);
        
        if (strlen($document) === 11) {
            return self::validate_cpf($document);
        } elseif (strlen($document) === 14) {
            return self::validate_cnpj($document);
        }
        
        return false;
    }
    
    /**
     * Formatar preço: 1234.56 -> R$ 1.234,56
     * 
     * @param float $value Valor numérico
     * @param bool $show_currency Mostrar símbolo R$
     * @return string Preço formatado
     */
    public static function format_price($value, $show_currency = true) {
        if ($value === null || $value === '') {
            return $show_currency ? 'R$ 0,00' : '0,00';
        }
        
        $formatted = number_format((float) $value, 2, ',', '.');
        
        return $show_currency ? "R$ {$formatted}" : $formatted;
    }
    
    /**
     * Sanitizar telefone (remover caracteres não numéricos)
     * 
     * @param string $phone Telefone com ou sem formatação
     * @return string Telefone apenas com números
     */
    public static function sanitize_phone($phone) {
        if (empty($phone)) {
            return '';
        }
        
        return preg_replace('/[^0-9]/', '', $phone);
    }
    
    /**
     * Formatar telefone: 11987654321 -> (11) 98765-4321
     * 
     * @param string $phone Telefone sem formatação
     * @return string Telefone formatado
     */
    public static function format_phone($phone) {
        if (empty($phone)) {
            return '';
        }
        
        $phone = self::sanitize_phone($phone);
        
        if (strlen($phone) === 11) {
            // Celular com 9 dígitos: (11) 98765-4321
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            // Fixo ou celular antigo: (11) 8765-4321
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $phone;
    }
    
    /**
     * Sanitizar CEP (remover caracteres não numéricos)
     * 
     * @param string $postal_code CEP com ou sem formatação
     * @return string CEP apenas com números
     */
    public static function sanitize_postal_code($postal_code) {
        if (empty($postal_code)) {
            return '';
        }
        
        return preg_replace('/[^0-9]/', '', $postal_code);
    }
    
    /**
     * Formatar CEP: 13400710 -> 13400-710
     * 
     * @param string $postal_code CEP sem formatação
     * @return string CEP formatado
     */
    public static function format_postal_code($postal_code) {
        if (empty($postal_code)) {
            return '';
        }
        
        $postal_code = self::sanitize_postal_code($postal_code);
        
        if (strlen($postal_code) !== 8) {
            return $postal_code;
        }
        
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $postal_code);
    }
    
    /**
     * Validar email
     * 
     * @param string $email Email para validar
     * @return bool True se válido
     */
    public static function validate_email($email) {
        if (empty($email)) {
            return false;
        }
        
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Truncar texto com reticências
     * 
     * @param string $text Texto original
     * @param int $length Comprimento máximo
     * @param string $suffix Sufixo (padrão: ...)
     * @return string Texto truncado
     */
    public static function truncate($text, $length = 100, $suffix = '...') {
        if (empty($text)) {
            return '';
        }
        
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return mb_substr($text, 0, $length) . $suffix;
    }
    
    /**
     * Gerar slug amigável para URL
     * 
     * @param string $text Texto original
     * @return string Slug
     */
    public static function slugify($text) {
        if (empty($text)) {
            return '';
        }
        
        // Converte para minúsculas
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove acentos
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        
        // Remove caracteres especiais
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        
        // Substitui espaços e múltiplos hífens por um único hífen
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Remove hífens do início e fim
        return trim($text, '-');
    }
    
    /**
     * Escapar HTML para segurança
     * 
     * @param string $text Texto para escapar
     * @return string Texto escapado
     */
    public static function escape_html($text) {
        if (empty($text)) {
            return '';
        }
        
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Obter primeiras palavras de um texto (para nome/sobrenome)
     * 
     * @param string $text Texto completo
     * @param int $words Quantidade de palavras
     * @return string Primeiras palavras
     */
    public static function get_first_words($text, $words = 1) {
        if (empty($text)) {
            return '';
        }
        
        $parts = explode(' ', trim($text));
        $result = array_slice($parts, 0, $words);
        
        return implode(' ', $result);
    }
    
    /**
     * Obter últimas palavras de um texto (para nome/sobrenome)
     * 
     * @param string $text Texto completo
     * @param int $words Quantidade de palavras
     * @return string Últimas palavras
     */
    public static function get_last_words($text, $words = 1) {
        if (empty($text)) {
            return '';
        }
        
        $parts = explode(' ', trim($text));
        
        if (count($parts) <= $words) {
            return implode(' ', $parts);
        }
        
        $result = array_slice($parts, -$words);
        
        return implode(' ', $result);
    }
}
