<?php
/**
 * Template de la page de maintenance avec mini-jeu
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
    
    <style>
        /* Variables CSS */
        :root {
            --maintenance-bg: <?php echo esc_attr($settings['background_color']); ?>;
            --maintenance-text: <?php echo esc_attr($settings['text_color']); ?>;
            --sisme-green: #ACBCA0;
            --sisme-orange: #D4A374;
        }
        
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--maintenance-bg);
            color: var(--maintenance-text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .maintenance-container {
            max-width: 600px;
            text-align: center;
            animation: fadeInUp 1s ease-out;
        }
        
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
        
        .maintenance-title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 300;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
            animation: fadeIn 2s ease-out;
        }
        
        .maintenance-message {
            font-size: clamp(1rem, 3vw, 1.2rem);
            margin-bottom: 40px;
            opacity: 0.9;
            white-space: pre-line;
            animation: fadeIn 2.5s ease-out;
        }
        
        /* Barre de progression */
        .maintenance-progress {
            width: 100%;
            height: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
            margin: 30px 0;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 1px 2px rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        
        .maintenance-progress-bar {
            height: 100%;
            width: 0%;
            border-radius: 6px;
            position: relative;
            background: linear-gradient(45deg, var(--sisme-green) 0%, var(--sisme-orange) 50%, var(--sisme-green) 100%);
            box-shadow: 0 0 10px rgba(172, 188, 160, 0.5), inset 0 1px 0 rgba(255,255,255,0.3);
            transition: width 0.3s ease;
        }
        
        .progress-percentage {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: bold;
            color: rgba(255,255,255,0.9);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .maintenance-progress-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 40%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0.3) 60%, transparent 100%);
            border-radius: 6px;
            animation: shine 2s ease-in-out infinite;
        }
        
        /* Interface du mini-jeu */
        .maintenance-game {
            margin: 30px 0;
            text-align: center;
        }
        
        .game-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 0.9em;
            flex-wrap: wrap;
        }
        
        .speed-display,
        .boost-display,
        .prestige-display {
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .flouze-display {
            background: rgba(255,215,0,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,215,0,0.3);
            color: #FFD700;
        }
        
        .maintenance-boost-btn {
            background: linear-gradient(45deg, var(--sisme-green), var(--sisme-orange));
            color: white;
            border: none;
            border-radius: 50%;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-desktop {
            display: inline-block;
            font-size: 2em;
            padding: 15px;
            width: 70px;
            height: 70px;
            line-height: 40px;
        }
        
        .btn-mobile {
            display: none;
            font-size: 2em;
            padding: 15px;
            width: 70px;
            height: 70px;
            line-height: 40px;
        }
        
        .maintenance-boost-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .maintenance-boost-btn:active {
            transform: translateY(0) scale(0.95);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .game-message {
            font-size: 0.9em;
            opacity: 0.8;
            margin-top: 10px;
            min-height: 20px;
        }
        
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
            color: var(--sisme-green);
        }
        
        .maintenance-contact {
            margin-top: 40px;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            border-left: 4px solid var(--sisme-green);
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
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        
        @keyframes flouzeGain {
            0% { 
                opacity: 1; 
                transform: translateY(0); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-30px); 
            }
        }
        
        @keyframes particleFade {
            0% { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-100px) scale(0.5); 
            }
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
            
            .btn-desktop {
                display: none;
            }
            
            .btn-mobile {
                display: inline-block;
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
            <?php echo wp_unslash(esc_html($settings['title'])); ?>
        </h1>
        
        <div class="maintenance-message">
            <?php echo wp_unslash(nl2br(esc_html($settings['message']))); ?>
        </div>
        
        <!-- Barre de progression avec pourcentage -->
        <div class="maintenance-progress" id="maintenanceProgress">
            <div class="maintenance-progress-bar" id="progressBar">
                <div class="progress-percentage" id="progressPercentage">0%</div>
            </div>
        </div>
        
        <!-- Interface du mini-jeu -->
        <div class="maintenance-game">
            <div class="game-stats">
                <span class="speed-display">Vitesse: <strong id="speedValue">0.010%/s</strong></span>
                <span class="boost-display">Boost: <strong id="boostValue">x1.0</strong></span>
                <span class="prestige-display">Niveau: <strong id="prestigeValue">1</strong></span>
                <span class="flouze-display">💰 <strong id="flouzeValue">0</strong></span>
            </div>
            
            <button class="maintenance-boost-btn" id="boostBtn">
                <span class="btn-desktop">🖱️</span>
                <span class="btn-mobile">👆🏻</span>
            </button>
            
            <div class="game-message" id="gameMessage">Maintenez la progression !</div>
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
        // Variables du jeu
        let progress = 0;
        let baseSpeed = 0.01;
        let boostFactor = 1.0;
        let sismeFlouze = 0;
        let flouzePerClick = 1;
        let prestigeLevel = 1;
        let isRunning = true;
        
        // Messages
        const messages = {
            0: "Maintenez la progression !",
            25: "🔧 Maintenance en cours...",
            50: "⚡ À mi-parcours !",
            75: "🚀 Presque terminé !",
            100: "🎉 Prestige disponible !"
        };
        
        const prestigeMessages = [
            "🏆 Maintenance optimisée !",
            "⭐ Expert technique !",
            "💎 Maître artisan !",
            "👑 Légende vivante !",
            "🌟 Divinité de la maintenance !"
        ];
        
        // Fonction de clic - seulement Flouze
        function boostMaintenance() {
            if (!isRunning) return;
            
            sismeFlouze += flouzePerClick;
            updateDisplay();
            
            const boostBtn = document.getElementById('boostBtn');
            if (boostBtn) {
                boostBtn.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    boostBtn.style.transform = 'scale(1)';
                }, 150);
            }
            
            showFlouzeGain();
        }
        
        // Progression automatique
        function autoProgress() {
            if (!isRunning) return;
            
            const finalSpeed = baseSpeed * boostFactor * (1 + prestigeLevel * 0.1);
            progress += finalSpeed;
            
            updateDisplay();
            checkPrestige();
        }
        
        // Mise à jour affichage
        function updateDisplay() {
            const displayProgress = Math.min(progress, 100);
            const finalSpeed = baseSpeed * boostFactor * (1 + prestigeLevel * 0.1);
            const speedPercent = (finalSpeed).toFixed(3);
            
            const progressBar = document.getElementById('progressBar');
            const progressPercentage = document.getElementById('progressPercentage');
            const speedDisplay = document.getElementById('speedValue');
            const boostDisplay = document.getElementById('boostValue');
            const prestigeDisplay = document.getElementById('prestigeValue');
            const flouzeDisplay = document.getElementById('flouzeValue');
            const gameMessage = document.getElementById('gameMessage');
            
            if (progressBar) progressBar.style.width = displayProgress + '%';
            if (progressPercentage) progressPercentage.textContent = Math.floor(displayProgress) + '%';
            if (speedDisplay) speedDisplay.textContent = speedPercent + '%/s';
            if (boostDisplay) boostDisplay.textContent = 'x' + boostFactor.toFixed(1);
            if (prestigeDisplay) prestigeDisplay.textContent = prestigeLevel;
            if (flouzeDisplay) flouzeDisplay.textContent = formatNumber(sismeFlouze);
            
            const progressKey = Math.floor(displayProgress / 25) * 25;
            if (messages[progressKey] && gameMessage) {
                gameMessage.textContent = messages[progressKey];
            }
        }
        
        // Formater nombres
        function formatNumber(num) {
            if (num < 1000) return num.toString();
            if (num < 1000000) return (num / 1000).toFixed(1) + 'K';
            if (num < 1000000000) return (num / 1000000).toFixed(1) + 'M';
            return (num / 1000000000).toFixed(1) + 'B';
        }
        
        // Vérifier prestige
        function checkPrestige() {
            if (progress >= 100) {
                progress = 0;
                prestigeLevel++;
                flouzePerClick += 1;
                sismeFlouze += prestigeLevel * 10;
                
                const messageIndex = Math.min(prestigeLevel - 2, prestigeMessages.length - 1);
                if (messageIndex >= 0) {
                    const gameMessage = document.getElementById('gameMessage');
                    if (gameMessage) gameMessage.textContent = prestigeMessages[messageIndex];
                }
                
                showPrestigeEffect();
                updateDisplay();
            }
        }
        
        // Afficher gain flouze
        function showFlouzeGain() {
            const gainText = document.createElement('div');
            const boostBtn = document.getElementById('boostBtn');
            
            gainText.textContent = '+' + flouzePerClick + '💰';
            gainText.style.cssText = `
                position: fixed;
                color: #FFD700;
                font-weight: bold;
                font-size: 16px;
                pointer-events: none;
                z-index: 1001;
                left: ${boostBtn ? boostBtn.getBoundingClientRect().left + 30 : 50}px;
                top: ${boostBtn ? boostBtn.getBoundingClientRect().top - 20 : 50}px;
                animation: flouzeGain 1s ease-out forwards;
            `;
            
            document.body.appendChild(gainText);
            setTimeout(() => {
                if (gainText.parentNode) gainText.parentNode.removeChild(gainText);
            }, 1000);
        }
        
        // Effet prestige
        function showPrestigeEffect() {
            for (let i = 0; i < 10; i++) {
                setTimeout(() => createParticle(), i * 100);
            }
            
            const progressBar = document.getElementById('progressBar');
            if (progressBar) {
                progressBar.style.boxShadow = '0 0 30px rgba(255,255,255,0.8)';
                setTimeout(() => {
                    progressBar.style.boxShadow = '0 0 10px rgba(172, 188, 160, 0.5), inset 0 1px 0 rgba(255,255,255,0.3)';
                }, 500);
            }
        }
        
        // Créer particule
        function createParticle() {
            const particle = document.createElement('div');
            particle.textContent = '✨';
            
            const maxX = window.innerWidth - 50;
            const maxY = window.innerHeight - 50;
            const x = Math.random() * maxX + 25;
            const y = Math.random() * maxY + 25;
            
            particle.style.cssText = `
                position: fixed;
                font-size: 20px;
                pointer-events: none;
                z-index: 1000;
                left: ${x}px;
                top: ${y}px;
                animation: particleFade 2s ease-out forwards;
            `;
            
            document.body.appendChild(particle);
            setTimeout(() => {
                if (particle.parentNode) particle.parentNode.removeChild(particle);
            }, 2000);
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            const boostBtn = document.getElementById('boostBtn');
            if (boostBtn) {
                boostBtn.addEventListener('click', boostMaintenance);
            }
            
            setInterval(autoProgress, 1000);
            setTimeout(updateDisplay, 100);
        });
        
        <?php if (!empty($settings['end_date'])): ?>
        // Countdown
        (function() {
            const endDate = new Date('<?php echo esc_js($settings['end_date']); ?>').getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endDate - now;
                
                if (distance < 0) return;
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                
                const countdownElement = document.querySelector('.maintenance-countdown strong');
                if (countdownElement) {
                    if (days > 0) {
                        countdownElement.innerHTML = `⏱️ Retour dans : ${days} jour${days > 1 ? 's' : ''} ${hours}h ${minutes}min`;
                    } else if (hours > 0) {
                        countdownElement.innerHTML = `⏱️ Retour dans : ${hours}h ${minutes}min`;
                    } else if (minutes > 0) {
                        countdownElement.innerHTML = `⏱️ Retour dans : ${minutes} minute${minutes > 1 ? 's' : ''}`;
                    } else {
                        countdownElement.innerHTML = '🎉 Retour imminent !';
                    }
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 60000);
        })();
        <?php endif; ?>
    </script>
</body>
</html>