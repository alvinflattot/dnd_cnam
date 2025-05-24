# Documentation des actions – Backend & Logique (D&D 5e)

## 1. Introduction

Cette documentation couvre les actions qu’un personnage peut effectuer **hors-combat** et **pendant le combat** dans Donjons & Dragons 5e, du point de vue de la logique métier backend. L’interface, la gestion des jets de dés, les statistiques de personnage ou l’animation graphique sont prises en charge par d’autres équipes ; ici, on définit les **services**, **contrats** et **flux** nécessaires pour orchestrer ces actions.

---

## 2. Actions hors-combat

### 2.1 Vue d’ensemble
Les actions hors-combat sont généralement non chronométrées et ne consomment pas le tour du personnage. Exemples :
- **Se déplacer** (exploration)
- **Examiner / fouiller**
- **Interaction sociale** (parler, marchander)
- **Utiliser un objet** (consommer une potion, lire un parchemin)
- **Repos court / long** (régénération, réinitialisation de ressources)

### 2.2 Spécifications Backend

| Action               | Entrées                                       | Traitement                                                                                  | Sorties / Événements générés        |
|----------------------|-----------------------------------------------|---------------------------------------------------------------------------------------------|-------------------------------------|
| Se déplacer          | position_actuelle, destination, vitesse       | Calcul du chemin (A\*), détection de collisions, déclenchement d’éventuels « rencontres »   | nouvelle_position, Liste<Rencontre> |
| Examiner / fouiller  | cible (objet, pièce), compétence (Perception) | Vérification difficulté (DD), génération de résultat d’examen                               | succès/échec, Détails trouvés       |
| Interaction sociale  | cible_personnage, type_interaction            | Application de modificateurs sociaux, mise à jour d’état relationnel                        | Narration, modifications d’état     |
| Utiliser un objet    | id_objet, cible_eventuelle                    | Vérification inventaire, application d’effets (soin, buff, etc.)                            | changements d’état, Journal d’action|
| Repos court / long   | durée                                         | Restauration de PV, récupération de ressources, réinitialisation des capacités incantatoires | Nouvel état du personnage           |

### 2.3 Flux et événements
- **Programmation événementielle** : chaque action hors-combat déclenche un événement centralisé (`EventDispatcher`), permettant aux autres services (journal de partie, log, triggers de quêtes) de s’abonner via le pattern **Observateur**.
- **Procuration (Proxy)** pour la couche de persistance : les appels aux services de mise à jour de base de données passent par un proxy afin de gérer la mise en cache et la journalisation.

---

## 3. Actions en combat

### 3.1 Vue d’ensemble
En combat, chaque personnage dispose d’un **tour** où il peut faire :
1. **Action** (attaquer, lancer un sort, utiliser un objet…)
2. **Bonus action** (action spéciale offerte par certaines capacités)
3. **Mouvement**
4. **Réaction** (hors de son tour lorsqu’un déclencheur survient)

### 3.2 Spécifications Backend

| Phase               | Action                                     | Entrées                                               | Traitement                                                          | Sorties / Événements            |
|---------------------|--------------------------------------------|-------------------------------------------------------|----------------------------------------------------------------------|---------------------------------|
| Tour principal      | Attaque                                    | attaquant, cible, arme, modificateurs                 | Calcul jet d’attaque vs CA, jet de dégâts si touché                  | Dégâts appliqués, logs combats  |
|                     | Sort                                       | lanceur, sort_id, cibles, ressources (emplacements)   | Vérification composantes, DD, application d’effets                   | Effets, animations à jouer      |
|                     | Utiliser un objet                          | id_objet, cible                                       | Similaire hors-combat mais consomme action                          | Mise à jour inventaire, effets  |
| Bonus action        | Bonus spécifique                           | dépend de la capacité                                 | Application immédiate d’un effet modulaire                           | État modifié                    |
| Mouvement           | Se déplacer                                | position, distance, obstacles                         | Mise à jour position, checks de zone (zone de contrôle adverse…)    | Position finale                 |
| Réaction            | Défensive (Riposte, Bouclier…)             | déclencheur (attaque adverse, sort)                   | Exécution avant que l’attaque/sort n’aboutisse si réussite           | Annulation partielle/totalité   |

### 3.3 Mécanismes

- **Stratégie** : modéliser les différents types d’attaques, sorts et actions spéciales comme des implémentations d’une interface `ActionCombat`. Au moment de l’exécution, on choisit la stratégie adaptée à l’action demandée.
- **Itérateur** : parcours ordonné du **tour** par initiative. Un `CombatManager` expose un itérateur sur les participants, qui, à chaque appel `next()`, renvoie le personnage suivant.
- **Singleton** : `CombatManager` et `EventDispatcher` sont instanciés une seule fois par rencontre de combat.
- **Décorateur** : pour enrichir dynamiquement les effets d’une attaque (par exemple, un buff temporaire ajoute un décorateur à l’action d’attaque pour augmenter les dégâts).

---

## 4. Patrons de conception préconisés

| Module / Composant           | Patron                    | Justification                                                                                  |
|------------------------------|---------------------------|-----------------------------------------------------------------------------------------------|
| Gestion des événements       | Observateur, Prog. Événementielle | Permet une architecture découplée, chaque service s’abonne aux événements                    |
| Persistance (DB/cache/log)   | Proxy, Singleton          | Proxy pour gérer cache/log, Singleton pour l’instance unique de la connexion à la BD         |
| Création d’actions           | Fabrication (Factory)     | Usine d’`Action` génériques selon type (Attaque, Sort, Mouvement…)                            |
| Exécution d’une action       | Stratégie                 | Chaque action de combat est une stratégie interchangeable                                    |
| Parcours du tour             | Itérateur                 | Itération claire et maintenable sur la file d’initiative                                     |
| Enrichissement des actions   | Décorateur                | Ajout dynamique de comportements (buff, malus, etc.)                                         |
| Services utilitaires         | Singleton                 | Services transverses (Logger, CombatManager)                                                 |
| Orchestration hors-combat    | Prog. Événementielle, Observateur | Permet de notifier différents sous-systèmes (quête, journal, carte) sans couplage          |

---

## 5. Cahier des charges

### 5.1 Contexte & objectifs
- **Contexte** : Application D&D 5e, équipe backend en charge de la logique des actions.
- **Objectif** : Fournir un API/service modulable, extensible pour gérer toutes les actions de personnages, hors-combat et en-combat.

### 5.2 Périmètre
- **Inclus** :
    - Définition des contrats (interfaces, DTO)
    - Implémentation des services d’exécution d’actions
    - Gestion des événements et notifications
    - Moteur de résolution des actions de combat
- **Exclus** :
    - Interface utilisateur
    - Gestion des jets de dés (fourni par un service externe)
    - Stockage des personnages et inventaires (fourni par d’autres équipes)
    - Tests automatisés

### 5.3 Exigences fonctionnelles
1. **RF1** – Exposer une fonction `executeHorsCombat(ActionHC $action): Event[]`.
2. **RF2** – Exposer une fonction `executeCombat(int $combatId, ActionCombat $action): Event[]`.
3. **RF3** – Chaque action doit publier un événement via `EventDispatcher::dispatch()`.
4. **RF4** – Gérer l’ordre d’initiative via un itérateur sur les participants.
5. **RF5** – Les buffs/débuffs s’ajoutent dynamiquement aux actions via décorateurs.

### 5.4 Exigences non-fonctionnelles
- **N1 – Scalabilité** : design modulaire, injection de dépendances manuelle.
- **N2 – Performance** : un tour de combat (jusqu’à 20 persos) en < 50 ms.
- **N3 – Extensibilité** : ajouter de nouveaux types d’actions sans modifier le code existant (Open/Closed).
- **N4 – Sécurité** : validation stricte des inputs, gestion des droits d’exécution.

### 5.5 Architecture & Technologies
- **Langage** : PHP 8
- **Sans framework** : micro-architecture, point d’entrée unique (`index.php`), routes et DI manuels
- **EventDispatcher** : classe singleton interne
- **Persistance** : PDO + Proxy pour cache/log
- **Gestion des erreurs** : Exceptions spécifiques, middleware d’erreur global

### 5.6 Livrables
1. Spécification des fonctions et DTO
2. Code source backend
3. Documentation technique (UML, séquence, architecture)
4. Guide d’intégration pour les autres équipes

### 5.7 Planning prévisionnel

| Étape                         | Durée estimée      |
|-------------------------------|--------------------|
| Analyse & Conception          | 1 semaine          |
| Mise en place de l’architecture | 1 semaine          |
| Développement Actions HC      | 2 semaines         |
| Développement Actions Combat  | 3 semaines         |
| Documentation & Livraison     | 1 semaine          |

### 5.8 Contraintes
- Respect strict du périmètre et des livrables
- Intégration avec les services de dés et persistance fournis par d’autres équipes
- Délais de déploiement court (2 mois max)



Exemple de personnage :
```json
{
  "name": "Eldon Brightshield",
  "hp": 12,
  "ac": 16,
  "extras": {
    "level": 1,
    "class": "Fighter",
    "race": "Human",
    "statistics": {
      "strength": 16,
      "dexterity": 13,
      "constitution": 14,
      "intelligence": 10,
      "wisdom": 12,
      "charisma": 8
    },
    "inventory": [
      "Longsword",
      "Shield",
      "Chain Mail",
      "Explorer's Pack",
      "10 javelins"
    ],
    "features": [
      "Second Wind"
    ],
    "spells": []
  }
}

```