/**
 * Logique complète du mini-jeu de maintenance
 * 
 * @file maintenance-game.js
 */

// Variables globales du jeu
let progress = 0;
let baseSpeed = 0.01;
let boostFactor = 1.0;
let robotSpeed = 0;
let sismeFlouze = 0;
let flouzePerClick = 1;
let flouzePerSecond = 0;
let prestigeLevel = 1;
let prestigePoints = 0;
let isRunning = true;

// Shop Flouze
let boostOwned = 0;
let robotOwned = 0;
let generatorOwned = 0;
let boostBasePrice = 10;
let robotBasePrice = 50;
let generatorBasePrice = 100;

// Shop Prestige
let boostEvolutionLevel = 1;
let robotEvolutionLevel = 1;
let boostEvolutionPrice = 1;
let robotEvolutionPrice = 1;

// Constantes
const baseBoostPower = 0.5;
const baseRobotSpeed = 0.05;
const baseFlouzePerSecond = 1;

// Messages de progression
const messages = {
    0: "Maintenez la progression !",
    25: "🔧 Maintenance en cours...",
    50: "⚡ À mi-parcours !",
    75: "🚀 Presque terminé !",
    100: "🎉 Prestige !"
};

/**
 * === FONCTIONS PRINCIPALES ===
 */

// Fonction de clic - génère du Flouze
function boostMaintenance() {
    if (!isRunning) return;
    
    sismeFlouze += flouzePerClick;
    updateDisplay();
    
    const btn = document.getElementById('boostBtn');
    if (btn) {
        btn.style.transform = 'scale(0.9)';
        setTimeout(() => {
            btn.style.transform = 'scale(1)';
        }, 150);
    }
    
    showFlouzeGain();
}

// Progression automatique
function autoProgress() {
    if (!isRunning) return;
    
    // Vitesse totale = (base + robots) × boost × bonus prestige
    const totalSpeed = (baseSpeed + robotSpeed) * boostFactor * (1 + prestigeLevel * 0.1);
    progress += totalSpeed / 10; // Divisé par 10 pour fluidité (appelé 10x plus souvent)
    
    updateDisplay();
    checkPrestige();
}

// Génération passive de Flouze
function generatePassiveFlouze() {
    if (!isRunning || flouzePerSecond <= 0) return;
    
    sismeFlouze += flouzePerSecond;
    updateDisplay();
}

// Vérifier le prestige
function checkPrestige() {
    if (progress >= 100) {
        // Auto-prestige sans modal
        progress = 0;
        prestigeLevel++;
        prestigePoints += 1; // +1 point de prestige
        flouzePerClick += 1;
        sismeFlouze += prestigeLevel * 10;
        
        showPrestigeEffect();
        updateDisplay();
    }
}

/**
 * === AFFICHAGE ===
 */

// Mise à jour complète de l'affichage
function updateDisplay() {
    const displayProgress = Math.min(progress, 100);
    const totalSpeed = (baseSpeed + robotSpeed) * boostFactor * (1 + prestigeLevel * 0.1);
    const speedPercent = (totalSpeed).toFixed(3);
    
    // Barre de progression
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    if (progressBar) progressBar.style.width = displayProgress + '%';
    if (progressPercentage) progressPercentage.textContent = Math.floor(displayProgress) + '%';
    
    // Stats
    const speedEl = document.getElementById('speedValue');
    const boostEl = document.getElementById('boostValue');
    const prestigeEl = document.getElementById('prestigeValue');
    const prestigePointsEl = document.getElementById('prestigePointsValue');
    const flouzeEl = document.getElementById('flouzeValue');
    const flouzePerSecEl = document.getElementById('flouzePerSecValue');
    
    if (speedEl) speedEl.textContent = speedPercent + '%/s';
    if (boostEl) boostEl.textContent = 'x' + boostFactor.toFixed(1);
    if (prestigeEl) prestigeEl.textContent = prestigeLevel;
    if (prestigePointsEl) prestigePointsEl.textContent = formatNumber(prestigePoints);
    if (flouzeEl) flouzeEl.textContent = formatNumber(sismeFlouze);
    if (flouzePerSecEl) flouzePerSecEl.textContent = formatNumber(flouzePerSecond);
    
    // Shop
    updateShopDisplay();
}

// Mettre à jour l'affichage du shop
function updateShopDisplay() {
    // Prix
    const boostPriceEl = document.getElementById('boostPrice');
    const robotPriceEl = document.getElementById('robotPrice');
    const generatorPriceEl = document.getElementById('generatorPrice');
    
    if (boostPriceEl) boostPriceEl.textContent = formatNumber(getBoostPrice());
    if (robotPriceEl) robotPriceEl.textContent = formatNumber(getRobotPrice());
    if (generatorPriceEl) generatorPriceEl.textContent = formatNumber(getGeneratorPrice());
    
    // Quantités possédées
    const boostOwnedEl = document.getElementById('boostOwned');
    const robotOwnedEl = document.getElementById('robotOwned');
    const generatorOwnedEl = document.getElementById('generatorOwned');
    
    if (boostOwnedEl) boostOwnedEl.textContent = boostOwned;
    if (robotOwnedEl) robotOwnedEl.textContent = robotOwned;
    if (generatorOwnedEl) generatorOwnedEl.textContent = generatorOwned;
    
    // Prestige
    const boostEvolutionLevelEl = document.getElementById('boostEvolutionLevel');
    const robotEvolutionLevelEl = document.getElementById('robotEvolutionLevel');
    const boostEvolutionPriceEl = document.getElementById('boostEvolutionPrice');
    const robotEvolutionPriceEl = document.getElementById('robotEvolutionPrice');
    
    if (boostEvolutionLevelEl) boostEvolutionLevelEl.textContent = boostEvolutionLevel;
    if (robotEvolutionLevelEl) robotEvolutionLevelEl.textContent = robotEvolutionLevel;
    if (boostEvolutionPriceEl) boostEvolutionPriceEl.textContent = boostEvolutionPrice;
    if (robotEvolutionPriceEl) robotEvolutionPriceEl.textContent = robotEvolutionPrice;
    
    // Activer/désactiver boutons
    const buyBoostBtn = document.getElementById('buyBoost');
    const buyRobotBtn = document.getElementById('buyRobot');
    const buyGeneratorBtn = document.getElementById('buyGenerator');
    const buyBoostEvolutionBtn = document.getElementById('buyBoostEvolution');
    const buyRobotEvolutionBtn = document.getElementById('buyRobotEvolution');
    
    if (buyBoostBtn) buyBoostBtn.disabled = sismeFlouze < getBoostPrice();
    if (buyRobotBtn) buyRobotBtn.disabled = sismeFlouze < getRobotPrice();
    if (buyGeneratorBtn) buyGeneratorBtn.disabled = sismeFlouze < getGeneratorPrice();
    if (buyBoostEvolutionBtn) buyBoostEvolutionBtn.disabled = prestigePoints < boostEvolutionPrice;
    if (buyRobotEvolutionBtn) buyRobotEvolutionBtn.disabled = prestigePoints < robotEvolutionPrice;
}

/**
 * === SHOP FLOUZE ===
 */

// Calculer prix avec progression
function getBoostPrice() {
    return Math.floor(boostBasePrice * Math.pow(1.5, boostOwned));
}

function getRobotPrice() {
    return Math.floor(robotBasePrice * Math.pow(1.8, robotOwned));
}

function getGeneratorPrice() {
    return Math.floor(generatorBasePrice * Math.pow(2.0, generatorOwned));
}

// Acheter boost
function buyBoost() {
    const price = getBoostPrice();
    if (sismeFlouze >= price) {
        sismeFlouze -= price;
        boostOwned++;
        boostFactor = 1.0 + (boostOwned * baseBoostPower * (boostEvolutionLevel * 0.3 + 0.7));
        updateDisplay();
        showPurchaseEffect('⚡');
    }
}

// Acheter robot
function buyRobot() {
    const price = getRobotPrice();
    if (sismeFlouze >= price) {
        sismeFlouze -= price;
        robotOwned++;
        robotSpeed = robotOwned * baseRobotSpeed * (robotEvolutionLevel * 0.3 + 0.7);
        updateDisplay();
        showPurchaseEffect('🤖');
    }
}

// Acheter générateur
function buyGenerator() {
    const price = getGeneratorPrice();
    if (sismeFlouze >= price) {
        sismeFlouze -= price;
        generatorOwned++;
        flouzePerSecond = generatorOwned * baseFlouzePerSecond;
        updateDisplay();
        showPurchaseEffect('💎');
    }
}

/**
 * === SHOP PRESTIGE ===
 */

// Acheter évolution boost
function buyBoostEvolution() {
    if (prestigePoints >= boostEvolutionPrice) {
        prestigePoints -= boostEvolutionPrice;
        boostEvolutionLevel++;
        boostEvolutionPrice = Math.floor(boostEvolutionPrice * 1.5);
        
        // Recalculer l'efficacité des boosts
        boostFactor = 1.0 + (boostOwned * baseBoostPower * (boostEvolutionLevel * 0.3 + 0.7));
        
        updateDisplay();
        showPurchaseEffect('⚡⭐');
    }
}

// Acheter évolution robot
function buyRobotEvolution() {
    if (prestigePoints >= robotEvolutionPrice) {
        prestigePoints -= robotEvolutionPrice;
        robotEvolutionLevel++;
        robotEvolutionPrice = Math.floor(robotEvolutionPrice * 1.5);
        
        // Recalculer l'efficacité des robots
        robotSpeed = robotOwned * baseRobotSpeed * (robotEvolutionLevel * 0.3 + 0.7);
        
        updateDisplay();
        showPurchaseEffect('🤖⭐');
    }
}

/**
 * === SYSTÈME D'ONGLETS ===
 */

function switchTab(tabName) {
    // Masquer tous les contenus
    const allContents = document.querySelectorAll('.tab-content');
    allContents.forEach(content => content.classList.remove('active'));
    
    // Désactiver tous les onglets
    const allTabs = document.querySelectorAll('.tab');
    allTabs.forEach(tab => tab.classList.remove('active'));
    
    // Activer le bon contenu et onglet
    const targetContent = document.getElementById(tabName + '-tab');
    const targetTab = document.querySelector(`[data-tab="${tabName}"]`);
    
    if (targetContent) targetContent.classList.add('active');
    if (targetTab) targetTab.classList.add('active');
}

/**
 * === EFFETS VISUELS ===
 */

// Afficher gain de flouze
function showFlouzeGain() {
    const gainText = document.createElement('div');
    const btn = document.getElementById('boostBtn');
    
    gainText.textContent = '+' + flouzePerClick + '💰';
    gainText.style.cssText = `
        position: fixed;
        color: #FFD700;
        font-weight: bold;
        font-size: 14px;
        pointer-events: none;
        z-index: 1001;
        left: ${btn ? btn.getBoundingClientRect().left + 25 : 50}px;
        top: ${btn ? btn.getBoundingClientRect().top - 15 : 50}px;
        animation: flouzeGain 1s ease-out forwards;
    `;
    
    document.body.appendChild(gainText);
    setTimeout(() => {
        if (gainText.parentNode) gainText.parentNode.removeChild(gainText);
    }, 1000);
}

// Effet d'achat
function showPurchaseEffect(emoji) {
    const effectText = document.createElement('div');
    effectText.textContent = emoji + ' Acheté !';
    effectText.style.cssText = `
        position: fixed;
        color: var(--sisme-green);
        font-weight: bold;
        font-size: 16px;
        pointer-events: none;
        z-index: 1001;
        left: 50%;
        top: 30%;
        transform: translateX(-50%);
        animation: flouzeGain 2s ease-out forwards;
    `;
    
    document.body.appendChild(effectText);
    setTimeout(() => {
        if (effectText.parentNode) effectText.parentNode.removeChild(effectText);
    }, 2000);
}

// Effet de prestige
function showPrestigeEffect() {
    for (let i = 0; i < 5; i++) {
        setTimeout(() => createParticle(), i * 100);
    }
}

// Créer particule
function createParticle() {
    const particle = document.createElement('div');
    particle.textContent = '✨';
    
    const maxX = window.innerWidth - 30;
    const maxY = window.innerHeight - 30;
    const x = Math.random() * maxX + 15;
    const y = Math.random() * maxY + 15;
    
    particle.style.cssText = `
        position: fixed;
        font-size: 16px;
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

/**
 * === UTILITAIRES ===
 */

// Formater les nombres
function formatNumber(num) {
    if (num < 1000) return num.toString();
    if (num < 1000000) return (num / 1000).toFixed(1) + 'K';
    if (num < 1000000000) return (num / 1000000).toFixed(1) + 'M';
    return (num / 1000000000).toFixed(1) + 'B';
}

/**
 * === GESTION DU TOGGLE JEU ===
 */

function toggleGame() {
    const gameContainer = document.getElementById('gameContainer');
    const toggleBtn = document.getElementById('toggleGame');
    
    if (!gameContainer || !toggleBtn) return;
    
    if (gameContainer.classList.contains('hidden')) {
        // Afficher le jeu
        gameContainer.classList.remove('hidden');
        gameContainer.classList.add('show');
        toggleBtn.textContent = '🎮 Masquer le jeu';
        toggleBtn.classList.add('active');
        
        // Démarrer le jeu si pas déjà fait
        if (!isRunning) {
            isRunning = true;
        }
    } else {
        // Masquer le jeu
        gameContainer.classList.add('hidden');
        gameContainer.classList.remove('show');
        toggleBtn.textContent = '🎮 Patienter en jouant';
        toggleBtn.classList.remove('active');
    }
}

/**
 * === INITIALISATION ===
 */

document.addEventListener('DOMContentLoaded', function() {
    // Event listener pour le toggle du jeu
    const toggleBtn = document.getElementById('toggleGame');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleGame);
    }
    
    // Event listeners pour les boutons de jeu
    const boostBtn = document.getElementById('boostBtn');
    const buyBoostBtn = document.getElementById('buyBoost');
    const buyRobotBtn = document.getElementById('buyRobot');
    const buyGeneratorBtn = document.getElementById('buyGenerator');
    const buyBoostEvolutionBtn = document.getElementById('buyBoostEvolution');
    const buyRobotEvolutionBtn = document.getElementById('buyRobotEvolution');
    
    if (boostBtn) boostBtn.addEventListener('click', boostMaintenance);
    if (buyBoostBtn) buyBoostBtn.addEventListener('click', buyBoost);
    if (buyRobotBtn) buyRobotBtn.addEventListener('click', buyRobot);
    if (buyGeneratorBtn) buyGeneratorBtn.addEventListener('click', buyGenerator);
    if (buyBoostEvolutionBtn) buyBoostEvolutionBtn.addEventListener('click', buyBoostEvolution);
    if (buyRobotEvolutionBtn) buyRobotEvolutionBtn.addEventListener('click', buyRobotEvolution);
    
    // Event listeners pour les onglets
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Démarrer les boucles de jeu
    setInterval(autoProgress, 100); // 10 fois par seconde pour fluidité
    setInterval(generatePassiveFlouze, 1000); // 1 fois par seconde pour Flouze passif
    
    // Première mise à jour
    setTimeout(updateDisplay, 100);
});

// Debug (à retirer en production)
function debugGame() {
    console.log('=== DEBUG GAME ===');
    console.log('Progress:', progress);
    console.log('Flouze:', sismeFlouze);
    console.log('Prestige Points:', prestigePoints);
    console.log('Boosts:', boostOwned);
    console.log('Robots:', robotOwned);
    console.log('Generators:', generatorOwned);
    console.log('Speed:', (baseSpeed + robotSpeed) * boostFactor * (1 + prestigeLevel * 0.1));
}