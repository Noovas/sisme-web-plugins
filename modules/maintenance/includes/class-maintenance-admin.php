<?php
/**
 * Interface d'administration du module maintenance
 * 
 * @file modules/maintenance/includes/class-maintenance-admin.php
 * @depends includes/class-maintenance-core.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sisme_Maintenance_Admin {
    
    private $core;
    private $module_url;
    
    public function __construct($core) {
        $this->core = $core;
        $this->module_url = SISME_PLUGIN_URL . 'modules/maintenance/';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('sisme_admin_display_module_settings', array($this, 'display_module_settings'), 10, 2);
    }
    
    /**
     * Chargement des assets admin
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'sisme-web-plugins') === false) {
            return;
        }
        
        wp_enqueue_style(
            'sisme-maintenance-admin',
            $this->module_url . 'assets/css/admin.css',
            array('sisme-admin-css'),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'sisme-maintenance-admin',
            $this->module_url . 'assets/js/admin.js',
            array('jquery', 'sisme-admin-js'),
            '1.0.0',
            true
        );
        
        wp_localize_script('sisme-maintenance-admin', 'sismeMaintenanceAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sisme_maintenance_nonce')
        ));
    }
    
    /**
     * Affichage des paramètres du module
     */
    public function display_module_settings($content, $module_name) {
        if ($module_name !== 'maintenance') {
            return $content;
        }
        
        $settings = $this->core->get_settings();
        $is_active = $this->core->is_maintenance_active();
        
        ob_start();
        ?>
        <div class="sisme-container">
            <h2>Paramètres de maintenance</h2>
            
            <!-- Statut actuel -->
            <div class="sisme-section">
                <h3>Statut actuel</h3>
                <div class="sisme-maintenance-status">
                    <div class="sisme-status-indicator <?php echo $is_active ? 'active' : 'inactive'; ?>">
                        <span class="sisme-status-dot"></span>
                        <strong><?php echo $is_active ? 'MAINTENANCE ACTIVÉE' : 'Site en ligne'; ?></strong>
                    </div>
                    
                    <button type="button" 
                            class="sisme-btn <?php echo $is_active ? 'sisme-btn-danger' : 'sisme-btn-primary'; ?> sisme-toggle-maintenance"
                            data-active="<?php echo $is_active ? '1' : '0'; ?>">
                        <?php echo $is_active ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
                    </button>
                </div>
            </div>
            
            <!-- Formulaire de configuration -->
            <form class="sisme-maintenance-form">
                <div class="sisme-section">
                    <h3>Contenu de la page</h3>
                    
                    <div class="sisme-form-group">
                        <label class="sisme-form-label" for="maintenance_title">Titre de la page</label>
                        <input type="text" 
                               id="maintenance_title" 
                               name="title" 
                               class="sisme-form-input"
                               value="<?php echo esc_attr($settings['title']); ?>"
                               placeholder="Site en maintenance">
                    </div>
                    
                    <div class="sisme-form-group">
                        <label class="sisme-form-label" for="maintenance_message">Message</label>
                        <textarea id="maintenance_message" 
                                  name="message" 
                                  class="sisme-form-textarea"
                                  rows="4"
                                  placeholder="Notre site est actuellement en maintenance..."><?php echo esc_textarea($settings['message']); ?></textarea>
                    </div>
                    
                    <div class="sisme-form-group">
                        <label class="sisme-form-label" for="maintenance_logo">URL du logo (optionnel)</label>
                        <input type="url" 
                               id="maintenance_logo" 
                               name="logo_url" 
                               class="sisme-form-input"
                               value="<?php echo esc_attr($settings['logo_url']); ?>"
                               placeholder="https://monsite.com/logo.png">
                    </div>
                    
                    <div class="sisme-form-group">
                        <label class="sisme-form-label" for="maintenance_end_date">Date de fin prévue (optionnel)</label>
                        <input type="datetime-local" 
                               id="maintenance_end_date" 
                               name="end_date" 
                               class="sisme-form-input"
                               value="<?php echo esc_attr($settings['end_date']); ?>">
                    </div>
                </div>
                
                <div class="sisme-section">
                    <h3>Apparence</h3>
                    
                    <div class="sisme-grid sisme-grid-2">
                        <div class="sisme-form-group">
                            <label class="sisme-form-label" for="maintenance_bg_color">Couleur de fond</label>
                            <input type="color" 
                                   id="maintenance_bg_color" 
                                   name="background_color" 
                                   class="sisme-form-input sisme-color-picker"
                                   value="<?php echo esc_attr($settings['background_color']); ?>">
                        </div>
                        
                        <div class="sisme-form-group">
                            <label class="sisme-form-label" for="maintenance_text_color">Couleur du texte</label>
                            <input type="color" 
                                   id="maintenance_text_color" 
                                   name="text_color" 
                                   class="sisme-form-input sisme-color-picker"
                                   value="<?php echo esc_attr($settings['text_color']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="sisme-section">
                    <div class="sisme-btn-group">
                        <button type="button" class="sisme-btn sisme-btn-secondary sisme-preview-maintenance">
                            Aperçu
                        </button>
                        <button type="submit" class="sisme-btn sisme-btn-primary">
                            Sauvegarder les paramètres
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Aperçu -->
            <div class="sisme-section sisme-maintenance-preview sisme-hidden">
                <h3>Aperçu de la page de maintenance</h3>
                <div class="sisme-preview-container">
                    <iframe class="sisme-preview-frame" src="about:blank"></iframe>
                </div>
            </div>
        </div>
        
        <!-- Messages de feedback -->
        <div class="sisme-maintenance-messages"></div>
        <?php
        
        return ob_get_clean();
    }
}