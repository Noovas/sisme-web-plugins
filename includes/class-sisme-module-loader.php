<?php
/**
 * Chargeur automatique des modules
 * 
 * @file includes/class-sisme-module-loader.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sisme_Module_Loader {
    
    private $modules_path;
    private $loaded_modules = array();
    
    public function __construct() {
        $this->modules_path = SISME_PLUGIN_PATH . 'modules/';
    }
    
    public function load_all_modules() {
        if (!is_dir($this->modules_path)) {
            return array();
        }
        
        $modules = $this->discover_modules();
        
        foreach ($modules as $module_name => $module_path) {
            $this->load_module($module_name, $module_path);
        }
        
        return $this->loaded_modules;
    }
    
    private function discover_modules() {
        $modules = array();
        $directories = glob($this->modules_path . '*', GLOB_ONLYDIR);
        
        foreach ($directories as $dir) {
            $module_name = basename($dir);
            $module_file = $dir . '/' . $module_name . '.php';
            
            if (file_exists($module_file)) {
                $modules[$module_name] = $module_file;
            }
        }
        
        return $modules;
    }
    
    private function load_module($module_name, $module_file) {
        if (file_exists($module_file)) {
            require_once $module_file;
            
            // Convention: classe principale = Sisme_Module_NomModule
            $class_name = 'Sisme_Module_' . ucfirst($module_name);
            
            if (class_exists($class_name)) {
                $this->loaded_modules[$module_name] = new $class_name();
            }
        }
    }
    
    public function get_loaded_modules() {
        return $this->loaded_modules;
    }
}