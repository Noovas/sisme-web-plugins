<?php
/**
 * Interface d'administration du plugin Sisme Web Plugins
 * 
 * @file includes/class-sisme-admin.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sisme_Admin {
    
    private $modules;
    
    public function __construct($modules) {
        $this->modules = $modules;
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'handle_form_submission'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Sisme Web Plugins',
            'Sisme Web',
            'manage_options',
            'sisme-web-plugins',
            array($this, 'display_admin_page'),
            'dashicons-admin-plugins',
            30
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_sisme-web-plugins') {
            return;
        }
        
        wp_enqueue_style(
            'sisme-admin-css',
            SISME_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SISME_VERSION
        );
        
        wp_enqueue_script(
            'sisme-admin-js',
            SISME_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            SISME_VERSION,
            true
        );
    }
    
    public function display_admin_page() {
        ?>
        <div class="wrap">
            <div class="sisme-container">
                <h1>Sisme Web Plugins</h1>
                <p>Gestion des modules web</p>
                
                <div class="sisme-grid sisme-grid-3">
                    <?php $this->display_modules(); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function display_modules() {
        if (empty($this->modules)) {
            echo '<p>Aucun module trouvé.</p>';
            return;
        }
        
        foreach ($this->modules as $module_name => $module_instance) {
            $this->display_module_card($module_name, $module_instance);
        }
    }
    
    private function display_module_card($module_name, $module_instance) {
        $module_info = $this->get_module_info($module_instance);
        $is_active = $this->is_module_active($module_name);
        $status_class = $is_active ? 'sisme-active' : 'sisme-inactive';
        
        ?>
        <div class="sisme-card <?php echo $status_class; ?>">
            <div class="sisme-card-header">
                <h3 class="sisme-card-title"><?php echo esc_html($module_info['name']); ?></h3>
                <p class="sisme-card-description"><?php echo esc_html($module_info['description']); ?></p>
            </div>
            
            <div class="sisme-card-footer">
                <div class="sisme-btn-group">
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('sisme_module_toggle', 'sisme_nonce'); ?>
                        <input type="hidden" name="module_name" value="<?php echo esc_attr($module_name); ?>">
                        <input type="hidden" name="action" value="<?php echo $is_active ? 'deactivate' : 'activate'; ?>">
                        <button type="submit" class="sisme-btn <?php echo $is_active ? 'sisme-btn-secondary' : 'sisme-btn-primary'; ?>">
                            <?php echo $is_active ? 'Désactiver' : 'Activer'; ?>
                        </button>
                    </form>
                    
                    <?php if ($is_active && method_exists($module_instance, 'has_settings') && $module_instance->has_settings()): ?>
                        <a href="<?php echo admin_url('admin.php?page=sisme-web-plugins&module=' . $module_name); ?>" class="sisme-btn sisme-btn-secondary">
                            Paramètres
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function get_module_info($module_instance) {
        if (method_exists($module_instance, 'get_info')) {
            return $module_instance->get_info();
        }
        
        return array(
            'name' => 'Module',
            'description' => 'Description non disponible'
        );
    }
    
    private function is_module_active($module_name) {
        $active_modules = get_option('sisme_active_modules', array());
        return in_array($module_name, $active_modules);
    }
    
    public function handle_form_submission() {
        if (!isset($_POST['sisme_nonce']) || !wp_verify_nonce($_POST['sisme_nonce'], 'sisme_module_toggle')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $module_name = sanitize_text_field($_POST['module_name']);
        $action = sanitize_text_field($_POST['action']);
        
        $active_modules = get_option('sisme_active_modules', array());
        
        if ($action === 'activate') {
            if (!in_array($module_name, $active_modules)) {
                $active_modules[] = $module_name;
            }
        } else {
            $active_modules = array_diff($active_modules, array($module_name));
        }
        
        update_option('sisme_active_modules', $active_modules);
        
        wp_redirect(admin_url('admin.php?page=sisme-web-plugins'));
        exit;
    }
}