<?php
/**
 * Classe principale du plugin Sisme Web Plugins
 * 
 * @file includes/class-sisme-core.php
 * @depends includes/class-sisme-module-loader.php
 * @depends includes/class-sisme-admin.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sisme_Core {
    
    private static $instance = null;
    private $modules = array();
    
    public static function init() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->load_modules();
        $this->init_admin();
    }
    
    private function load_dependencies() {
        require_once SISME_PLUGIN_PATH . 'includes/class-sisme-module-loader.php';
        require_once SISME_PLUGIN_PATH . 'includes/class-sisme-admin.php';
    }
    
    private function load_modules() {
        $loader = new Sisme_Module_Loader();
        $this->modules = $loader->load_all_modules();
    }
    
    private function init_admin() {
        if (is_admin()) {
            new Sisme_Admin($this->modules);
        }
    }
    
    public function get_modules() {
        return $this->modules;
    }
    
    public static function activate() {
        // Actions lors de l'activation
        if (!current_user_can('activate_plugins')) {
            return;
        }
    }
    
    public static function deactivate() {
        // Actions lors de la désactivation
        if (!current_user_can('activate_plugins')) {
            return;
        }
    }
}