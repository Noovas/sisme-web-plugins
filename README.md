# Sisme Web Plugins

Plugin WordPress modulaire avec système de chargement automatique des modules.

## 🚀 Installation

1. Téléchargez le plugin
2. Placez le dossier `sisme-web-plugins` dans `/wp-content/plugins/`
3. Activez le plugin dans l'admin WordPress
4. Accédez au menu "Sisme Web" dans l'admin

## 📦 Architecture

```
sisme-web-plugins/
├── sisme-web-plugins.php              # Fichier principal
├── includes/                          # Classes principales
│   ├── class-sisme-core.php
│   ├── class-sisme-module-loader.php
│   └── class-sisme-admin.php
├── assets/                            # Assets globaux
│   ├── css/admin.css                  # CSS modulaire réutilisable
│   └── js/admin.js                    # JS modulaire réutilisable
└── modules/                           # Modules (auto-découverte)
    └── maintenance/                   # Module maintenance
        ├── maintenance.php            # Fichier principal du module
        ├── includes/                  # Classes du module
        ├── assets/                    # Assets du module
        └── assets/templates/          # Templates personnalisables
```

## 🔥 Fonctionnalités

### ✅ Système modulaire
- **Auto-découverte** : Nouveau module = nouveau dossier, c'est tout !
- **Architecture MVC** : Code organisé et maintenable
- **Assets isolés** : Chaque module gère ses propres CSS/JS
- **Configuration centralisée** : Un seul tableau de bord

### ✅ CSS/JS réutilisable
- **Système de composants** : `.sisme-card`, `.sisme-btn`, `.sisme-grid`...
- **Variables CSS** : Cohérence visuelle garantie
- **Responsive** : Mobile-first par défaut
- **Extensible** : Ajoutez vos composants facilement

### ✅ Module Maintenance inclus
- **Mode maintenance** : Activation/désactivation en un clic
- **Template personnalisable** : Couleurs, logo, message
- **Countdown dynamique** : Date de fin avec décompte temps réel
- **Aperçu en direct** : Voir les modifications instantanément
- **Responsive** : Parfait sur tous les écrans

## 🛠️ Créer un nouveau module

### 1. Structure de base

Créez un dossier dans `modules/` avec cette structure :

```
modules/mon-module/
├── mon-module.php                     # OBLIGATOIRE : Point d'entrée
├── includes/
│   ├── class-mon-module-core.php      # Logique métier
│   └── class-mon-module-admin.php     # Interface admin
└── assets/
    ├── css/admin.css                  # Styles spécifiques
    └── js/admin.js                    # Scripts spécifiques
```

### 2. Fichier principal (mon-module.php)

```php
<?php
class Sisme_Module_MonModule {
    
    public function __construct() {
        // Chargement des dépendances
    }
    
    public function get_info() {
        return array(
            'name' => 'Mon Module',
            'description' => 'Description de mon module'
        );
    }
    
    public function has_settings() {
        return true; // Si le module a des paramètres
    }
}
```

### 3. Utiliser les composants CSS

```html
<!-- Container avec effet de profondeur -->
<div class="sisme-container">
    <h2>Mon titre</h2>
    
    <!-- Grille responsive -->
    <div class="sisme-grid sisme-grid-3">
        <!-- Cartes -->
        <div class="sisme-card">
            <div class="sisme-card-header">
                <h3 class="sisme-card-title">Titre</h3>
                <p class="sisme-card-description">Description</p>
            </div>
            <div class="sisme-card-footer">
                <button class="sisme-btn sisme-btn-primary">Action</button>
            </div>
        </div>
    </div>
    
    <!-- Formulaire -->
    <form class="sisme-form">
        <div class="sisme-form-group">
            <label class="sisme-form-label">Libellé</label>
            <input type="text" class="sisme-form-input">
        </div>
        <div class="sisme-btn-group">
            <button class="sisme-btn sisme-btn-secondary">Annuler</button>
            <button class="sisme-btn sisme-btn-primary">Valider</button>
        </div>
    </form>
</div>
```

## 🎨 Classes CSS disponibles

### Containers et layout
- `.sisme-container` : Container avec profondeur automatique
- `.sisme-section` : Section avec bordure et ombre
- `.sisme-grid` : Grille responsive
- `.sisme-grid-2|3|4` : Colonnes spécifiques

### Cartes
- `.sisme-card` : Carte de base
- `.sisme-card-header|footer` : En-tête et pied de carte
- `.sisme-card-title|description` : Titre et description

### Boutons
- `.sisme-btn` : Bouton de base
- `.sisme-btn-primary|secondary|danger` : Variantes de couleur
- `.sisme-btn-group` : Groupe de boutons

### Formulaires
- `.sisme-form-group` : Groupe de champ
- `.sisme-form-label|input|textarea|select` : Éléments de formulaire

### Alertes
- `.sisme-alert` : Alerte de base
- `.sisme-alert-success|warning|error|info` : Types d'alerte

### États
- `.sisme-active|inactive` : États actif/inactif
- `.sisme-loading` : État de chargement
- `.sisme-hidden|visible` : Visibilité

## 📱 Responsive

Le système est mobile-first avec des breakpoints automatiques :
- **Mobile** : < 600px
- **Tablet** : 600px - 768px  
- **Desktop** : > 768px

## 🔧 Hooks et filtres

### Pour les modules

```php
// Afficher les paramètres d'un module
add_filter('sisme_admin_display_module_settings', function($content, $module_name) {
    if ($module_name === 'mon-module') {
        return 'HTML de mes paramètres';
    }
    return $content;
}, 10, 2);
```

### JavaScript disponible

```javascript
// Utilitaires globaux
SismeAdmin.showMessage('Message', 'success');
SismeAdmin.validateForm($form);
SismeAdmin.createRippleEffect($button, event);

// Composants
SismeAdmin.initTabs();
SismeAdmin.initToggles();
SismeAdmin.initTooltips();
```

## 🚨 Bonnes pratiques

### Nommage
- **Modules** : `mon-module` (kebab-case)
- **Classes PHP** : `Sisme_Module_MonModule` (Snake_Case avec majuscules)
- **CSS** : `.sisme-mon-composant` (kebab-case avec préfixe)
- **JS** : `SismeMonModule` (PascalCase)

### Sécurité
```php
// Toujours vérifier les autorisations
if (!current_user_can('manage_options')) {
    wp_die('Permissions insuffisantes');
}

// Nonces pour les formulaires
wp_nonce_field('mon_action', 'mon_nonce');
wp_verify_nonce($_POST['mon_nonce'], 'mon_action');

// Échapper les sorties
echo esc_html($data);
echo esc_attr($attribute);
echo esc_url($url);
```

### Performance
- Charger les assets uniquement sur les pages concernées
- Utiliser les hooks WordPress appropriés
- Éviter les requêtes inutiles

## 🎯 Module Maintenance

### Utilisation rapide
1. Allez dans "Sisme Web" → Cliquez sur "Paramètres" du module Maintenance
2. Configurez le titre, message, couleurs
3. Cliquez sur "Activer la maintenance"
4. Votre site affiche la page de maintenance (sauf pour les admins)

### Fonctionnalités
- ✅ Activation/désactivation en un clic
- ✅ Personnalisation complète (couleurs, logo, message)
- ✅ Date de fin avec countdown temps réel
- ✅ Aperçu en direct des modifications
- ✅ Template personnalisable
- ✅ Code HTTP 503 pour le SEO
- ✅ Responsive design

### Template personnalisé
Copiez `modules/maintenance/assets/templates/maintenance-page.php` dans votre thème et modifiez-le.

## 🆘 Support

- **Documentation** : Ce fichier README
- **Code** : Commenté et autodocumenté
- **Architecture** : Modulaire et extensible

---

*Créé avec ❤️ pour Sisme Web*