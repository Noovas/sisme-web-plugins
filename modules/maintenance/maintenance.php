<?php
/**
 * Module de maintenance pour Sisme Web Plugins
 * 
 * @file modules/maintenance/maintenance.php
 * @depends includes/class-maintenance-core.php
 * @depends includes/class-maintenance-admin.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale du module maintenance
 * Suit la convention : Sisme_Module_NomModule
 */
class Sisme_Module_Maintenance {
    
    private $module_path;
    private $module_url;
    private $core;
    private $admin;
    
    public function __construct() {
        $this->module_path = SISME_PLUGIN_PATH . 'modules/maintenance/';
        $this->module_url = SISME_PLUGIN_URL . 'modules/maintenance/';
        
        $this->load_dependencies();
        $this->init_components();
    }
    
    private function load_dependencies() {
        require_once $this->module_path . 'includes/class-maintenance-core.php';
        require_once $this->module_path . 'includes/class-maintenance-admin.php';
    }
    
    private function init_components() {
        $this->core = new Sisme_Maintenance_Core();
        
        if (is_admin()) {
            $this->admin = new Sisme_Maintenance_Admin($this->core);
        }
    }
    
    /**
     * Informations du module pour le tableau de bord
     */
    public function get_info() {
        return array(
            'name' => 'Maintenance',
            'description' => 'Mode maintenance personnalisable pour votre site'
        );
    }
    
    /**
     * Le module a-t-il des paramètres ?
     */
    public function has_settings() {
        return true;
    }
    
    /**
     * URL d'accès aux paramètres
     */
    public function get_settings_url() {
        return admin_url('admin.php?page=sisme-web-plugins&module=maintenance');
    }
}