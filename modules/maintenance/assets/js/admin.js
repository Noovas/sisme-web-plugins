/**
 * JavaScript d'administration du module maintenance
 * 
 * @file modules/maintenance/assets/js/admin.js
 * @depends assets/js/admin.js (JS global)
 */

(function($) {
    'use strict';

    // Objet principal du module maintenance
    window.SismeMaintenanceAdmin = {
        
        // Initialisation
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        // Événements
        bindEvents: function() {
            $(document).on('click', '.sisme-toggle-maintenance', this.toggleMaintenance);
            $(document).on('submit', '.sisme-maintenance-form', this.saveSettings);
            $(document).on('click', '.sisme-preview-maintenance', this.showPreview);
            $(document).on('change', '.sisme-maintenance-form input, .sisme-maintenance-form textarea', this.updatePreview);
        },
        
        // Initialisation des composants
        initComponents: function() {
            this.initColorPickers();
            this.initDatePicker();
        },
        
        // === GESTION DE LA MAINTENANCE ===
        
        // Activer/désactiver la maintenance
        toggleMaintenance: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const isActive = $button.data('active') === '1';
            const newState = !isActive;
            
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: sismeMaintenanceAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'sisme_toggle_maintenance',
                    active: newState ? '1' : '0',
                    nonce: sismeMaintenanceAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SismeMaintenanceAdmin.updateMaintenanceStatus(newState);
                        SismeMaintenanceAdmin.showMessage(response.data.message, 'success');
                    } else {
                        SismeMaintenanceAdmin.showMessage(response.data || 'Erreur inconnue', 'error');
                    }
                },
                error: function() {
                    SismeMaintenanceAdmin.showMessage('Erreur de connexion', 'error');
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },
        
        // Mise à jour du statut visuel
        updateMaintenanceStatus: function(isActive) {
            const $indicator = $('.sisme-status-indicator');
            const $button = $('.sisme-toggle-maintenance');
            const $container = $('.sisme-maintenance-status');
            
            if (isActive) {
                $indicator.removeClass('inactive').addClass('active');
                $indicator.find('strong').text('MAINTENANCE ACTIVÉE');
                $button.removeClass('sisme-btn-primary').addClass('sisme-btn-danger');
                $button.text('Désactiver la maintenance');
                $button.data('active', '1');
            } else {
                $indicator.removeClass('active').addClass('inactive');
                $indicator.find('strong').text('Site en ligne');
                $button.removeClass('sisme-btn-danger').addClass('sisme-btn-primary');
                $button.text('Activer la maintenance');
                $button.data('active', '0');
            }
        },
        
        // === GESTION DES PARAMÈTRES ===
        
        // Sauvegarde des paramètres
        saveSettings: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const formData = $form.serializeArray();
            
            // Ajouter les données AJAX
            formData.push(
                { name: 'action', value: 'sisme_save_maintenance_settings' },
                { name: 'nonce', value: sismeMaintenanceAjax.nonce }
            );
            
            $form.addClass('saving');
            
            $.ajax({
                url: sismeMaintenanceAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        SismeMaintenanceAdmin.showMessage('Paramètres sauvegardés', 'success');
                        SismeMaintenanceAdmin.updatePreview();
                    } else {
                        SismeMaintenanceAdmin.showMessage(response.data || 'Erreur de sauvegarde', 'error');
                    }
                },
                error: function() {
                    SismeMaintenanceAdmin.showMessage('Erreur de connexion', 'error');
                },
                complete: function() {
                    $form.removeClass('saving');
                }
            });
        },
        
        // === GESTION DE L'APERÇU ===
        
        // Afficher l'aperçu
        showPreview: function(e) {
            e.preventDefault();
            
            const $preview = $('.sisme-maintenance-preview');
            
            if ($preview.hasClass('sisme-hidden')) {
                $preview.removeClass('sisme-hidden');
                SismeMaintenanceAdmin.updatePreview();
                $(this).text('Masquer l\'aperçu');
            } else {
                $preview.addClass('sisme-hidden');
                $(this).text('Aperçu');
            }
        },
        
        // Mise à jour de l'aperçu
        updatePreview: function() {
            const $frame = $('.sisme-preview-frame');
            
            if ($frame.length === 0 || $('.sisme-maintenance-preview').hasClass('sisme-hidden')) {
                return;
            }
            
            const settings = SismeMaintenanceAdmin.getFormSettings();
            const html = SismeMaintenanceAdmin.generatePreviewHTML(settings);
            
            const doc = $frame[0].contentDocument || $frame[0].contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();
        },
        
        // Récupération des paramètres du formulaire
        getFormSettings: function() {
            const $form = $('.sisme-maintenance-form');
            
            return {
                title: $form.find('[name="title"]').val() || 'Site en maintenance',
                message: $form.find('[name="message"]').val() || 'Notre site est actuellement en maintenance.',
                background_color: $form.find('[name="background_color"]').val() || '#f8f9fa',
                text_color: $form.find('[name="text_color"]').val() || '#333333',
                logo_url: $form.find('[name="logo_url"]').val() || '',
                end_date: $form.find('[name="end_date"]').val() || ''
            };
        },
        
        // Génération du HTML d'aperçu
        generatePreviewHTML: function(settings) {
            let logoHtml = '';
            if (settings.logo_url) {
                logoHtml = `
                    <div class="maintenance-logo">
                        <img src="${settings.logo_url}" alt="Logo" style="max-width: 200px; height: auto;">
                    </div>
                `;
            }
            
            let endDateHtml = '';
            if (settings.end_date) {
                const endDate = new Date(settings.end_date);
                const formattedDate = endDate.toLocaleDateString('fr-FR') + ' à ' + endDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                endDateHtml = `
                    <div class="maintenance-countdown">
                        <strong>Retour prévu le : ${formattedDate}</strong>
                    </div>
                `;
            }
            
            return `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>${settings.title}</title>
                    <style>
                        body {
                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            margin: 0;
                            padding: 0;
                            background: ${settings.background_color};
                            color: ${settings.text_color};
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
                            white-space: pre-line;
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
                        ${logoHtml}
                        <h1 class="maintenance-title">${settings.title}</h1>
                        <div class="maintenance-message">${settings.message}</div>
                        ${endDateHtml}
                    </div>
                </body>
                </html>
            `;
        },
        
        // === COMPOSANTS ===
        
        // Initialisation des sélecteurs de couleur
        initColorPickers: function() {
            $('.sisme-color-picker').on('change', function() {
                SismeMaintenanceAdmin.updatePreview();
            });
        },
        
        // Initialisation du sélecteur de date
        initDatePicker: function() {
            const $endDate = $('#maintenance_end_date');
            
            // Date minimum = maintenant
            const now = new Date();
            const minDate = now.getFullYear() + '-' + 
                          String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(now.getDate()).padStart(2, '0') + 'T' +
                          String(now.getHours()).padStart(2, '0') + ':' +
                          String(now.getMinutes()).padStart(2, '0');
            
            $endDate.attr('min', minDate);
        },
        
        // === UTILITAIRES ===
        
        // Affichage d'un message
        showMessage: function(message, type = 'info') {
            const $container = $('.sisme-maintenance-messages');
            const $message = $(`
                <div class="sisme-maintenance-message ${type}">
                    ${message}
                </div>
            `);
            
            $container.append($message);
            
            // Auto-suppression après 5 secondes
            setTimeout(function() {
                $message.fadeOut(function() {
                    $message.remove();
                });
            }, 5000);
        }
    };
    
    // Initialisation au chargement
    $(document).ready(function() {
        if ($('.sisme-maintenance-form').length > 0) {
            SismeMaintenanceAdmin.init();
        }
    });
    
})(jQuery);