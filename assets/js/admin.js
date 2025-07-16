/**
 * JavaScript modulaire pour l'administration Sisme Web Plugins
 * 
 * @file assets/js/admin.js
 * @system Composants réutilisables
 */

(function($) {
    'use strict';

    // Objet principal Sisme
    window.SismeAdmin = {
        
        // Initialisation
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        // Événements globaux
        bindEvents: function() {
            // Confirmation pour les actions dangereuses
            $(document).on('click', '.sisme-btn-danger', this.confirmAction);
            
            // Loading sur les formulaires
            $(document).on('submit', 'form', this.showLoading);
            
            // Auto-save pour les formulaires
            $(document).on('change', '.sisme-auto-save', this.autoSave);
        },
        
        // Initialisation des composants
        initComponents: function() {
            this.initTabs();
            this.initToggles();
            this.initTooltips();
        },
        
        // === COMPOSANTS RÉUTILISABLES ===
        
        // Système d'onglets
        initTabs: function() {
            $('.sisme-tabs').each(function() {
                const $tabs = $(this);
                const $tabButtons = $tabs.find('.sisme-tab-button');
                const $tabContents = $tabs.find('.sisme-tab-content');
                
                $tabButtons.on('click', function(e) {
                    e.preventDefault();
                    const target = $(this).data('tab');
                    
                    $tabButtons.removeClass('active');
                    $(this).addClass('active');
                    
                    $tabContents.removeClass('active');
                    $('#' + target).addClass('active');
                });
            });
        },
        
        // Système de toggles
        initToggles: function() {
            $('.sisme-toggle').on('change', function() {
                const $toggle = $(this);
                const target = $toggle.data('toggle');
                const $target = $('#' + target);
                
                if ($toggle.is(':checked')) {
                    $target.slideDown();
                } else {
                    $target.slideUp();
                }
            });
        },
        
        // Tooltips simples
        initTooltips: function() {
            $('.sisme-tooltip').each(function() {
                const $element = $(this);
                const title = $element.attr('title');
                
                if (title) {
                    $element.removeAttr('title');
                    $element.append('<span class="sisme-tooltip-text">' + title + '</span>');
                }
            });
        },
        
        // === UTILITAIRES ===
        
        // Confirmation d'action
        confirmAction: function(e) {
            const message = $(this).data('confirm') || 'Êtes-vous sûr ?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        },
        
        // Affichage du loading
        showLoading: function() {
            const $form = $(this);
            const $submit = $form.find('[type="submit"]');
            
            $submit.prop('disabled', true);
            $form.addClass('sisme-loading');
            
            // Restaurer après 10 secondes max
            setTimeout(function() {
                $submit.prop('disabled', false);
                $form.removeClass('sisme-loading');
            }, 10000);
        },
        
        // Auto-save
        autoSave: function() {
            const $field = $(this);
            const $form = $field.closest('form');
            
            // Debounce pour éviter trop de requêtes
            clearTimeout($field.data('timeout'));
            $field.data('timeout', setTimeout(function() {
                SismeAdmin.performAutoSave($form);
            }, 1000));
        },
        
        // Exécution de l'auto-save
        performAutoSave: function($form) {
            const formData = $form.serialize();
            const action = $form.attr('action') || window.location.href;
            
            $.ajax({
                url: action,
                type: 'POST',
                data: formData + '&auto_save=1',
                success: function(response) {
                    SismeAdmin.showMessage('Sauvegarde automatique effectuée', 'success');
                },
                error: function() {
                    SismeAdmin.showMessage('Erreur lors de la sauvegarde', 'error');
                }
            });
        },
        
        // Affichage de messages
        showMessage: function(message, type) {
            type = type || 'info';
            
            const $message = $('<div class="sisme-alert sisme-alert-' + type + '">' + message + '</div>');
            
            // Insérer au début de la page
            $('.sisme-container').prepend($message);
            
            // Masquer après 5 secondes
            setTimeout(function() {
                $message.fadeOut(function() {
                    $message.remove();
                });
            }, 5000);
        },
        
        // Validation de formulaire
        validateForm: function($form) {
            let isValid = true;
            
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val()) {
                    $field.addClass('sisme-error');
                    isValid = false;
                } else {
                    $field.removeClass('sisme-error');
                }
            });
            
            return isValid;
        },
        
        // Gestion des modules
        toggleModule: function(moduleName, action) {
            const data = {
                action: 'sisme_toggle_module',
                module: moduleName,
                toggle_action: action,
                nonce: sismeAjax.nonce
            };
            
            $.post(sismeAjax.ajaxurl, data, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    SismeAdmin.showMessage('Erreur lors du toggle du module', 'error');
                }
            });
        }
    };
    
    // Initialisation au chargement
    $(document).ready(function() {
        SismeAdmin.init();
    });
    
})(jQuery);