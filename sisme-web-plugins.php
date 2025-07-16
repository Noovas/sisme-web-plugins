<?php
/**
 * Plugin Name: Sisme Web Plugins
 * Description: Collection de modules web pour WordPress
 * Version: 1.0.0
 * Author: Sisme
 * 
 * @file sisme-web-plugins.php
 * @depends includes/class-sisme-core.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Constantes
define('SISME_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SISME_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SISME_VERSION', '1.0.0');

// Chargement du core
require_once SISME_PLUGIN_PATH . 'includes/class-sisme-core.php';

// Activation du plugin
register_activation_hook(__FILE__, array('Sisme_Core', 'activate'));
register_deactivation_hook(__FILE__, array('Sisme_Core', 'deactivate'));

// Démarrage
add_action('plugins_loaded', array('Sisme_Core', 'init'));