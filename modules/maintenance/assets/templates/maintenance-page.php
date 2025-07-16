<?php
/**
 * Template personnalisable de la page de maintenance
 * 
 * @file modules/maintenance/assets/templates/maintenance-page.php
 * 
 * Variables disponibles :
 * $settings - Tableau des paramètres de maintenance
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo esc_html($settings['title']); ?></title>
    
    <style>
        /* Variables CSS */
        :root {
            --maintenance-bg: <?php echo esc_attr($settings['background_color']); ?>;
            --maintenance-text: <?php echo esc_attr($settings['text_color']); ?>;
            --maintenance-accent: #3498db;
        }
        
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--maintenance-bg);
            color: var(--maintenance-text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Conteneur principal */
        .maintenance-container {
            max-width: 600px;
            text-align: center;
            animation: fadeInUp 1s ease-out;
        }
        
        /* Logo */
        .maintenance-logo {
            margin-bottom: 40px;
            animation: fadeIn 1.5s ease-out;
        }
        
        .maintenance-logo img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        /* Titre */
        .maintenance-title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 300;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
            animation: fadeIn 2s ease-out;
        }
        
        /* Message */
        .maintenance-message {
            font-size: clamp(1rem, 3vw, 1.2rem);
            margin-bottom: 40px;
            opacity: 0.9;
            white-space: pre-line;
            animation: fadeIn 2.5s ease-out;
        }
        
        /* Compteur */
        .maintenance-countdown {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            margin-top: 40px;
            border: 1px solid rgba(255,255,255,0.2);
            animation: fadeIn 3s ease-out;
        }
        
        .maintenance-countdown strong {
            font-size: 1.1em;
            color: var(--maintenance-accent);
        }
        
        /* Barre de progression optionnelle */
        .maintenance-progress {
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
            margin: 30px 0;
            overflow: hidden;
        }
        
        .maintenance-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--maintenance-accent), #2ecc71);
            border-radius: 2px;
            animation: progress 3s ease-in-out infinite;
        }
        
        /* Contact info optionnel */
        .maintenance-contact {
            margin-top: 40px;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            border-left: 4px solid var(--maintenance-accent);
            font-size: 0.9em;
            opacity: 0.8;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 0 10px;
            }
            
            .maintenance-logo img {
                max-width: 150px;
            }
            
            .maintenance-countdown,
            .maintenance-contact {
                padding: 15px;
            }
        }
        
        /* Mode sombre automatique */
        @media (prefers-color-scheme: dark) {
            :root {
                --maintenance-accent: #74b9ff;
            }
            
            .maintenance-countdown,
            .maintenance-contact {
                background: rgba(0,0,0,0.3);
                border-color: rgba(255,255,255,0.3);
            }
        }
    </style>
</head>

<body>
    <div class="maintenance-container">
        <?php if (!empty($settings['logo_url'])): ?>
            <div class="maintenance-logo">
                <img src="<?php echo esc_url($settings['logo_url']); ?>" 
                     alt="<?php bloginfo('name'); ?>" 
                     loading="lazy">
            </div>
        <?php endif; ?>
        
        <h1 class="maintenance-title">
            <?php echo esc_html($settings['title']); ?>
        </h1>
        
        <div class="maintenance-message">
            <?php echo nl2br(esc_html($settings['message'])); ?>
        </div>
        
        <!-- Barre de progression décorative -->
        <div class="maintenance-progress">
            <div class="maintenance-progress-bar"></div>
        </div>
        
        <?php if (!empty($settings['end_date'])): ?>
            <div class="maintenance-countdown">
                <strong>🔧 Retour prévu le : <?php echo date('d/m/Y à H:i', strtotime($settings['end_date'])); ?></strong>
            </div>
        <?php endif; ?>
        
        <!-- Informations de contact optionnelles -->
        <div class="maintenance-contact">
            <strong>💬 Besoin d'aide ?</strong><br>
            Contactez-nous à : <?php echo antispambot(get_option('admin_email')); ?>
        </div>
    </div>
    
    <!-- Script pour le countdown dynamique -->
    <script>
        <?php if (!empty($settings['end_date'])): ?>
        (function() {
            const endDate = new Date('<?php echo esc_js($settings['end_date']); ?>').getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endDate - now;
                
                if (distance < 0) {
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                
                if (days > 0) {
                    document.querySelector('.maintenance-countdown strong').innerHTML = 
                        `⏱️ Retour dans : ${days} jour${days > 1 ? 's' : ''} ${hours}h ${minutes}min`;
                } else if (hours > 0) {
                    document.querySelector('.maintenance-countdown strong').innerHTML = 
                        `⏱️ Retour dans : ${hours}h ${minutes}min`;
                } else if (minutes > 0) {
                    document.querySelector('.maintenance-countdown strong').innerHTML = 
                        `⏱️ Retour dans : ${minutes} minute${minutes > 1 ? 's' : ''}`;
                } else {
                    document.querySelector('.maintenance-countdown strong').innerHTML = 
                        '🎉 Retour imminent !';
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 60000); // Mise à jour chaque minute
        })();
        <?php endif; ?>
    </script>
</body>
</html>