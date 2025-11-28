<?php defined('ABSPATH') || exit;
class Rodust_Admin_Menu {
    private static $instance = null;
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() {}
}
