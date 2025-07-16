<?php
/**
 * Template de maintenance ultra-compact - HTML/PHP seulement
 * 
 * @file modules/maintenance/assets/templates/maintenance-page.php
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
    <link rel="stylesheet" href="<?php echo SISME_PLUGIN_URL; ?>modules/maintenance/assets/css/maintenance-styles.css">
</head>

<body>
    <div class="maintenance-container">
        <?php if (!empty($settings['logo_url'])): ?>
            <div class="maintenance-logo">
                <img src="<?php echo esc_url($settings['logo_url']); ?>" alt="<?php bloginfo('name'); ?>">
            </div>
        <?php endif; ?>
        
        <h1 class="maintenance-title">
            <?php echo wp_unslash(esc_html($settings['title'])); ?>
        </h1>
        
        <div class="maintenance-message">
            <?php echo wp_unslash(nl2br(esc_html($settings['message']))); ?>
        </div>
        
        <!-- Barre de progression -->
        <div class="maintenance-progress">
            <div class="maintenance-progress-bar" id="progressBar">
                <div class="progress-percentage" id="progressPercentage">0%</div>
            </div>
        </div>
        
        <!-- Jeu ultra-compact -->
        <div class="game-compact">
            <!-- Stats en grille 3x2 -->
            <div class="stats-compact">
                <div class="stat flouze">💰 <span id="flouzeValue">0</span></div>
                <div class="stat prestige">⭐ <span id="prestigePointsValue">0</span></div>
                <div class="stat">Niv.<span id="prestigeValue">1</span></div>
                <div class="stat">Vitesse: <span id="speedValue">0.01%/s</span></div>
                <div class="stat">Boost: <span id="boostValue">x1.0</span></div>
                <div class="stat">💎 <span id="flouzePerSecValue">0</span>/s</div>
            </div>
            
            <!-- Bouton de clic -->
            <button class="click-btn" id="boostBtn">
                <span class="btn-desktop">🖱️</span>
                <span class="btn-mobile">👆🏻</span>
            </button>
            
            <!-- Onglets -->
            <div class="tabs">
                <button class="tab active" data-tab="shop">🛒 Shop</button>
                <button class="tab" data-tab="prestige">⭐ Prestige</button>
            </div>
            
            <!-- Shop Flouze -->
            <div class="tab-content active" id="shop-tab">
                <div class="shop-item">
                    <div class="item-info">⚡ Boost x1.5 (<span id="boostOwned">0</span>)</div>
                    <button class="buy-btn" id="buyBoost">💰<span id="boostPrice">10</span></button>
                </div>
                <div class="shop-item">
                    <div class="item-info">🤖 Robot +0.05%/s (<span id="robotOwned">0</span>)</div>
                    <button class="buy-btn" id="buyRobot">💰<span id="robotPrice">50</span></button>
                </div>
                <div class="shop-item">
                    <div class="item-info">💎 +1💰/s (<span id="generatorOwned">0</span>)</div>
                    <button class="buy-btn" id="buyGenerator">💰<span id="generatorPrice">100</span></button>
                </div>
            </div>
            
            <!-- Shop Prestige -->
            <div class="tab-content" id="prestige-tab">
                <div class="shop-item">
                    <div class="item-info">⚡ Efficacité Boost (Niv.<span id="boostEvolutionLevel">1</span>)</div>
                    <button class="buy-btn prestige-btn" id="buyBoostEvolution">⭐<span id="boostEvolutionPrice">1</span></button>
                </div>
                <div class="shop-item">
                    <div class="item-info">🤖 Efficacité Robot (Niv.<span id="robotEvolutionLevel">1</span>)</div>
                    <button class="buy-btn prestige-btn" id="buyRobotEvolution">⭐<span id="robotEvolutionPrice">1</span></button>
                </div>
            </div>
        </div>
        
        <?php if (!empty($settings['end_date'])): ?>
            <div class="maintenance-countdown">
                <strong>🔧 Retour prévu le : <?php echo date('d/m/Y à H:i', strtotime($settings['end_date'])); ?></strong>
            </div>
        <?php endif; ?>
        
        <div class="maintenance-contact">
            <strong>💬 Besoin d'aide ?</strong><br>
            Contactez-nous à : <?php echo antispambot(get_option('admin_email')); ?>
        </div>
    </div>
    
    <script>
        // Variables CSS injectées depuis PHP
        const maintenanceColors = {
            bg: '<?php echo esc_js($settings['background_color']); ?>',
            text: '<?php echo esc_js($settings['text_color']); ?>',
            sismeGreen: '#ACBCA0',
            sismeOrange: '#D4A374'
        };
        
        // Initialiser les couleurs CSS
        document.documentElement.style.setProperty('--maintenance-bg', maintenanceColors.bg);
        document.documentElement.style.setProperty('--maintenance-text', maintenanceColors.text);
    </script>
    <script src="<?php echo SISME_PLUGIN_URL; ?>modules/maintenance/assets/js/maintenance-game.js"></script>
    
    <?php if (!empty($settings['end_date'])): ?>
    <script>
        // Countdown PHP vers JS
        const endDate = new Date('<?php echo esc_js($settings['end_date']); ?>').getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) return;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            const countdownEl = document.querySelector('.maintenance-countdown strong');
            if (countdownEl) {
                if (days > 0) {
                    countdownEl.innerHTML = `⏱️ Retour dans : ${days} jour${days > 1 ? 's' : ''} ${hours}h ${minutes}min`;
                } else if (hours > 0) {
                    countdownEl.innerHTML = `⏱️ Retour dans : ${hours}h ${minutes}min`;
                } else if (minutes > 0) {
                    countdownEl.innerHTML = `⏱️ Retour dans : ${minutes} minute${minutes > 1 ? 's' : ''}`;
                } else {
                    countdownEl.innerHTML = '🎉 Retour imminent !';
                }
            }
        }
        
        updateCountdown();
        setInterval(updateCountdown, 60000);
    </script>
    <?php endif; ?>
</body>
</html>