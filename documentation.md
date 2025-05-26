## Documentation des Actions – Backend & Logique (D&D 5e)

**Version Améliorée**

---

### Table des matières
1. [Introduction](#introduction)
2. [Vue d’ensemble des actions](#vue-densemble-des-actions)
3. [Actions hors-combat](#actions-hors-combat)
4. [Actions en combat](#actions-en-combat)
5. [Contrats et DTO](#contrats-et-dto)
6. [Flux et orchestration](#flux-et-orchestration)
7. [Patrons de conception](#patrons-de-conception)
8. [Exigences fonctionnelles et non-fonctionnelles](#exigences-fonctionnelles-et-non-fonctionnelles)
9. [Architecture, technologies et intégration](#architecture-technologies-et-intégration)
10. [Livrables et planning](#livrables-et-planning)
11. [Annexes](#annexes)

---

## 1. Introduction
Cette documentation définit les **services**, **contrats** et **flux** nécessaires pour orchestrer les actions des personnages dans une application D&D 5e. Elle cible la logique métier backend, sans inclure les interfaces utilisateur, la gestion des jets de dés (service externe) ni la persistance des personnages/inventaires (fournie par d’autres équipes).

**Objectifs** :
- Clarifier et unifier les interactions backend pour toutes les actions (hors-combat et combat).
- Garantir évolutivité, maintenabilité et performance.
- Faciliter l’intégration inter-équipes via des contrats clairs.

---

## 2. Vue d’ensemble des actions
| Catégorie        | Détail                             | Impact sur le tour | Temps            |
|------------------|------------------------------------|--------------------|------------------|
| Hors-combat      | Exploration, interactions sociales | N/A                | Non chronométré  |
| Combat           | Attaque, sort, mouvement, réaction | Oui                | Tour par tour    |

---

## 3. Actions hors-combat  <a name="actions-hors-combat"></a>
### 3.1 Description Générale
Les actions hors-combat n’utilisent pas le système de tours. Elles sont déclenchées à la demande et traitées immédiatement.

### 3.2 Spécifications Techniques
| Action              | Entrées                                       | Traitement                                                                                  | Sorties / Événements                |
|---------------------|-----------------------------------------------|---------------------------------------------------------------------------------------------|-------------------------------------|
| Déplacement         | `positionActuelle`, `destination`, `vitesse`  | A* pathfinding, détection collisions, triggers rencontres                                   | `nouvellePosition`, `Liste<Rencontre>` |
| Examiner / Fouiller | `cible`, `compétencePerception`, `DD`         | Vérification DD, génération résultat (succès/échec)                                         | `Échec/Réussite`, `DétailsTrouvés`     |
| Interaction Sociale | `ciblePersonnage`, `typeInteraction`          | Application modificateurs, mise à jour relationnel                                          | `Narration`, `ÉtatRelationnel`        |
| Utiliser un Objet   | `objetId`, `cible`                            | Vérification inventaire, application effets (soin, buff)                                    | `ÉtatPersonnage`, `JournalAction`     |
| Repos (court/long)  | `typeRepos` (court/long)                      | Restauration PV, récupération ressources, reset emplacements de sort                        | `NouvelÉtatPersonnage`              |

**Contrats** :
- Interface `HorsCombatService` exposant `executeHorsCombat(ActionHC $action): Event[]`
- DTO `ActionHC` (type, paramètres) et `Event` (type, payload)

---

## 4. Actions en combat  <a name="actions-en-combat"></a>
### 4.1 Cycle de Combat
1. **Initiative** – calcul + tri, itérateur sur participants
2. **Tour de chaque acteur** :
  - Action principale
  - Action bonus
  - Mouvement
  - Actions libres
3. **Réactions** hors-tour selon déclencheurs

### 4.2 Spécifications Techniques
| Phase            | Action                          | Entrées                                      | Traitement                                                             | Sorties / Événements            |
|------------------|---------------------------------|----------------------------------------------|-------------------------------------------------------------------------|---------------------------------|
| Tour principal   | Attaque                         | attaquant, cible, arme, modifs               | Jet attaque vs CA, jet dégâts si touché, logs                           | `Dégâts`, `LogsCombat`          |
|                  | Sort                            | lanceur, sortId, cibles, ressources          | Vérif compo, jet DD, application effets, gestion AOE                    | `EffetsAppliqués`, `Animations` |
|                  | Utiliser objet                  | objetId, cible                               | Traitement identique hors-combat + consomme action                      | `Effets`, `InventaireMisAJour`  |
| Bonus action     | Spécifique (Second Wind, etc.)  | personnage, capacité                         | Application immédiate via stratégie dédiée                              | `ÉtatPersonnage`                |
| Mouvement        | Déplacement                      | `position`, `distance`, `obstacles`          | A* ou ligne droite, checks zone de contrôle                             | `PositionFinale`                |
| Réaction         | Défensive (Riposte, Bouclier)   | `déclencheur` (attaque adverse, sort)        | Exécution pré-attaque si conditions OK                                  | `AttaqueInterrompue`, `ModifsCA` |

**Contrats** :
- Interface `CombatService` avec `executeCombat(int $combatId, ActionCombat $action): Event[]`
- DTO `ActionCombat` (phase, sous-type, params)
- Gestionnaire d’initiative implémentant `Iterator`
- Événements combat via `EventDispatcher::dispatch(Event $e)`

---

## 5. Contrats et DTO  <a name="contrats-et-dto"></a>
### 5.1 Principaux objets
```php
// Hors-combat
class ActionHC {
  string $type;
  array $params;
}

// Combat
class ActionCombat {
  string $phase;
  string $type;
  array $params;
  int $combatId;
}

// Événements
class Event {
  string $name;
  array $payload;
  DateTimeInterface $timestamp;
}
```
### 5.2 EventDispatcher
- Singleton interne
- Méthode `dispatch(Event $e): void`
- Utilise le patron Observateur pour notifier les abonnés

## 6. Flux et orchestration
1. **Requête API** → contrôleur front → instancie `ActionHC` / `ActionCombat`
2. **Validation** du DTO → injection de dépendances
3. **Service** `execute*` → produit liste d’`Event`
4. **EventDispatcher** → diffuse chaque événement
  - Quête, journal, UI, logs, etc.
5. **Réponse API** renvoyant les events et l’état final

## 7. Patrons de conception
| Contexte                 | Patron                      | Usage                                                          |
|--------------------------|-----------------------------|----------------------------------------------------------------|
| Création d’actions       | Factory                     | `ActionFactory::create($type, $params)`                       |
| Exécution selon type     | Strategy                    | `AttackStrategy`, `SpellStrategy`, `MoveStrategy`              |
| Enrichissement dynamique | Decorator                   | Buffs/débuffs dynamiques sur `ActionInterface`                 |
| Parcours initiative      | Iterator                    | `InitiativeOrderIterator`                                     |
| Événements               | Observer / EventDispatcher  | Diffusion découplée vers journal, quêtes, UI                   |
| Persistance cache/log    | Proxy                       | `DbProxy` pour cache et log, implémentant `PDO`               |
| Services transverses     | Singleton                   | Logger, `CombatManager`, `CacheService`                        |

## 8. Exigences fonctionnelles et non-fonctionnelles

### 8.1 Exigences Fonctionnelles
- **RF1** : `executeHorsCombat(ActionHC $action): Event[]`
- **RF2** : `executeCombat(int $combatId, ActionCombat $action): Event[]`
- **RF3** : Tout `Event` doit être `dispatch`é via `EventDispatcher`
- **RF4** : Initiative gérée via un `Iterator`
- **RF5** : Buffs/débuffs modulaires via décorateurs

### 8.2 Exigences Non-Fonctionnelles
- **N1 (Scalabilité)** : Modules découplés, injection de dépendances manuelle
- **N2 (Performance)** : Tour de combat < 50 ms pour ≤ 20 persos
- **N3 (Extensibilité)** : Respect du principe Open/Closed (OCP)
- **N4 (Sécurité)** : Validation stricte des inputs, gestion des droits
- **N5 (Observabilité)** : Logging complet, métriques (temps, taux d’erreur)

## 9. Architecture, technologies et intégration
- **Langage** : PHP 8.1+
- **Architecture** : micro-architecture, point d’entrée unique (`index.php`), routes manuelles
- **DI** : conteneur léger ou manuel
- **Persistance** : PDO + Proxy (cache/log)
- **Events** : `EventDispatcher` singleton
- **Exceptions** : classes dédiées, middleware global
- **Intégrations externes** :
  - Service de jets de dés (RPC/HTTP)
  - Service de persistance des personnages/inventaires

## 10. Livrables et planning
| Livrable                          | Description                          | Échéance     |
|-----------------------------------|--------------------------------------|--------------|
| Spécification fonctions & DTO     | UML, interfaces, exemples JSON       | Semaine 1    |
| Implémentation services & patterns| Code source, tests unitaires         | Semaines 2–3 |
| Documentation technique complète  | UML, séquences, guides d’intégration | Semaine 4    |
| Revue et validation inter-équipes | Sessions feedback, ajustements       | Semaine 5    |

## 11. Annexes
- ### **Exemple de personnage**
```json
{
  "name": "Eldon Brightshield",
  "hp": 12,
  "armorClass": 16,
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
    "10 javelins",
    "hpPotion"
  ],
  "features": [
    "Second Wind"
  ],
  "spells": []
}
```

### **Déroulement d'une attaque**
1. **Vous décidez d’attaquer**  
  Vous annoncez au maître de jeu (MJ) que vous voulez porter une attaque contre une cible (un monstre, un ennemi…). Par exemple : « J’attaque l’orque avec mon’épée ».

2. **Le jet d’attaque**  
  Pour savoir si vous touchez la cible, vous lancez un dé à vingt faces (un d20) et ajoutez deux nombres :
   - Votre modificateur de caractéristique (force pour une épée, dextérité pour un arc…)
   - Votre bonus de maîtrise (un bonus fixe lié à votre niveau si vous êtes compétent avec cette arme)

   **Formule :** Jet d’attaque = d20 + modificateur + maîtrise (si compétent)
   
   Le MJ compare ce total à la **Classe d’Armure (CA)** de la cible, qui est un nombre fixe.
   - Si votre total est au moins égal à la CA, vous **touchez**.
   - Sinon, vous **ratez**.

3. **Le jet de dégâts**  
  Si vous touchez, vous lancez le(s) dé(s) de dégâts de votre arme :
   - Par exemple, une épée longue fait **1d8** dégâts.
   - À ce résultat, on ajoute **encore** votre modificateur de caractéristique (le même que pour toucher).

   **Formule :** Dégâts = dé(s) de l’arme + modificateur

    Le nombre obtenu est la quantité de points de vie (PV) soustraits aux PV du monstre.

4. **Effets spéciaux et coups critiques**
   - **Coup critique :** si vous sortez un **20 naturel** sur le d20 (avant d’ajouter modifs), c’est un succès spécial : vous doublez les dés de dégâts (par exemple **2d8** au lieu de 1d8).
   - **Aptitudes de classe :** certains personnages ont des bonus (comme l’« Attaque sournoise » pour le voleur) qui ajoutent des dés de dégâts supplémentaires sous certaines conditions.

5. **Application des dégâts**
   - Le MJ retire vos dégâts des PV de la cible.
   - Si les PV de la créature tombent à 0 ou moins, elle est vaincue (K.O. ou morte).
   - Sinon, elle réagit (riposte, fuie, etc.) et le combat continue.


