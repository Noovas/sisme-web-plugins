<?php
/**
 * Logique principale du module maintenance
 * 
 * @file modules/maintenance/includes/class-maintenance-core.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sisme_Maintenance_Core {
    
    private $option_name = 'sisme_maintenance_settings';
    
    public function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('template_redirect', array($this, 'check_maintenance_mode'));
        add_action('wp_ajax_sisme_toggle_maintenance', array($this, 'ajax_toggle_maintenance'));
        add_action('wp_ajax_sisme_save_maintenance_settings', array($this, 'ajax_save_settings'));
    }
    
    /**
     * Vérification du mode maintenance
     */
    public function check_maintenance_mode() {
        if (!$this->is_maintenance_active()) {
            return;
        }
        
        // Exclure les admins
        if (current_user_can('manage_options')) {
            return;
        }
        
        // Exclure les pages d'admin
        if (is_admin() || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            return;
        }
        
        // Afficher la page de maintenance
        $this->display_maintenance_page();
        exit;
    }
    
    /**
     * Affichage de la page de maintenance
     */
    private function display_maintenance_page() {
        $settings = $this->get_settings();
        
        // Code HTTP 503 pour les moteurs de recherche
        status_header(503);
        header('Retry-After: 3600');
        
        // Charger le template
        $template_path = $this->get_template_path();
        
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            // Template par défaut
            $this->display_default_template($settings);
        }
    }
    
    /**
     * Template par défaut
     */
    private function display_default_template($settings) {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html($settings['title']); ?></title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    margin: 0;
                    padding: 0;
                    background: <?php echo esc_attr($settings['background_color']); ?>;
                    color: <?php echo esc_attr($settings['text_color']); ?>;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    text-align: center;
                }
                .maintenance-container {
                    max-width: 600px;
                    padding: 40px 20px;
                }
                .maintenance-logo {
                    margin-bottom: 30px;
                }
                .maintenance-logo img {
                    max-width: 200px;
                    height: auto;
                }
                .maintenance-title {
                    font-size: 2.5em;
                    margin-bottom: 20px;
                    font-weight: 300;
                }
                .maintenance-message {
                    font-size: 1.2em;
                    line-height: 1.6;
                    margin-bottom: 30px;
                    opacity: 0.9;
                }
                .maintenance-countdown {
                    font-size: 1.1em;
                    margin-top: 40px;
                    opacity: 0.8;
                }
            </style>
        </head>
        <body>
            <div class="maintenance-container">
                <?php if (!empty($settings['logo_url'])): ?>
                    <div class="maintenance-logo">
                        <img src="<?php echo esc_url($settings['logo_url']); ?>" alt="Logo">
                    </div>
                <?php endif; ?>
                
                <h1 class="maintenance-title"><?php echo wp_unslash(esc_html($settings['title'])); ?></h1>
                <div class="maintenance-message"><?php echo wp_unslash(wpautop(esc_html($settings['message']))); ?></div>
                
                <?php if (!empty($settings['end_date'])): ?>
                    <div class="maintenance-countdown">
                        <strong>Retour prévu le : <?php echo date('d/m/Y à H:i', strtotime($settings['end_date'])); ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Chemin du template personnalisé
     */
    private function get_template_path() {
        return $this->get_module_path() . 'assets/templates/maintenance-page.php';
    }
    
    /**
     * Paramètres par défaut
     */
    public function get_default_settings() {
        return array(
            'active' => false,
            'title' => 'Site en maintenance',
            'message' => 'Notre site est actuellement en maintenance. Nous serons bientôt de retour !',
            'background_color' => '#f8f9fa',
            'text_color' => '#333333',
            'logo_url' => '',
            'end_date' => ''
        );
    }
    
    /**
     * Récupération des paramètres
     */
    public function get_settings() {
        $settings = get_option($this->option_name, array());
        return wp_parse_args($settings, $this->get_default_settings());
    }
    
    /**
     * Sauvegarde des paramètres
     */
    public function save_settings($settings) {
        $current_settings = $this->get_settings();
        $new_settings = wp_parse_args($settings, $current_settings);
        
        // Validation
        $new_settings['active'] = !empty($new_settings['active']);
        $new_settings['title'] = sanitize_text_field($new_settings['title']);
        $new_settings['message'] = sanitize_textarea_field(wp_unslash($new_settings['message']));
        $new_settings['background_color'] = sanitize_hex_color($new_settings['background_color']);
        $new_settings['text_color'] = sanitize_hex_color($new_settings['text_color']);
        $new_settings['logo_url'] = esc_url_raw($new_settings['logo_url']);
        $new_settings['end_date'] = sanitize_text_field($new_settings['end_date']);
        
        return update_option($this->option_name, $new_settings);
    }
    
    /**
     * Le maintenance est-il actif ?
     */
    public function is_maintenance_active() {
        $settings = $this->get_settings();
        return !empty($settings['active']);
    }
    
    /**
     * AJAX : Activer/désactiver la maintenance
     */
    public function ajax_toggle_maintenance() {
        check_ajax_referer('sisme_maintenance_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $active = !empty($_POST['active']);
        $settings = $this->get_settings();
        $settings['active'] = $active;
        
        if ($this->save_settings($settings)) {
            wp_send_json_success(array(
                'active' => $active,
                'message' => $active ? 'Maintenance activée' : 'Maintenance désactivée'
            ));
        } else {
            wp_send_json_error('Erreur lors de la sauvegarde');
        }
    }
    
    /**
     * AJAX : Sauvegarder les paramètres
     */
    public function ajax_save_settings() {
        check_ajax_referer('sisme_maintenance_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        if ($this->save_settings($_POST)) {
            wp_send_json_success('Paramètres sauvegardés');
        } else {
            wp_send_json_error('Erreur lors de la sauvegarde');
        }
    }
    
    /**
     * Chemin du module
     */
    private function get_module_path() {
        return SISME_PLUGIN_PATH . 'modules/maintenance/';
    }
}