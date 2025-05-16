**1. Documentation des actions D&D 5e – Scope Backend Logique Métier (sans dés, sans persistance, sans gestion de personnages)**

Cette documentation couvre exclusivement la logique métier des actions (combat & exploration), en mémoire, à partir de données fournies par d’autres équipes (résultats de jets, informations de personnage, stockage). Les modules doivent être découplés de toute I/O, dés, DB ou gestion de persos.

---

### 1.1. Modules et responsabilités

- **ActionFactory**
  - Patron **Factory Method** pour instancier les objets `Action` selon leur type et configuration.
  - Chaque `Action` encapsule la logique `execute(context, inputData): ActionResult`.

- **ActionProcessor (Engine)**
  - Orchestrateur recevant un `ActionRequest { actionId, actorData, targetData, rollResult, params }`.
  - Utilise **Strategy** pour déléguer à la classe `Action` correspondante.
  - Retourne `ActionResult { effects, eventsToTrigger }`.

- **CombatOrchestrator**
  - Méthode `resolveTurn(turnData): TurnResult` gère :
    1. tri des actions principale/bonus/mouvement/réactions selon priorité
    2. appel séquentiel à `ActionProcessor` pour chaque action
    3. collecte des `ReactionRequest` générés

- **ReactionHandler**
  - Patron **Observer / Publisher-Subscriber** sur les événements (`onMove`, `onHit`, `onSpellCast`).
  - Pour chaque `Event`, exécute les `Reaction` correspondantes via `ActionProcessor`.

- **RuleValidator**
  - Patron **Chain of Responsibility** pour vérifier :
    1. portée et ligne de vue
    2. disponibilité de l’action (déjà utilisée ?)
    3. conditions d’état (avantage/désavantage, étourdi)
  - Renvoie liste d’erreurs métier si invalidité.

---

### 1.2. Flux d’exécution d’une action

1. **Réception** : l’API frontend (ou orchestrateur global) envoie un `ActionRequest`.
2. **Validation** : `RuleValidator` vérifie les préconditions métier.
3. **Exécution** : `ActionFactory` crée l’instance, `ActionProcessor` appelle `execute()`, reçoit `ActionResult`.
4. **Réactions** : les `eventsToTrigger` sont publiés à `ReactionHandler`.
5. **Retour** : agrégation `TurnResult` ou `ActionResult` envoyé au caller.

---

## 2. Cahier des charges technique – Service d’exécution d’actions

### 2.1. Objectif

Fournir un microservice stateless en PHP (classes pures + DI) exposant l’API `performAction` et `resolveTurn` sur des contexts fournis, sans dés, DB, ni gestion de personnages.

### 2.2. Interfaces publiques

- **performAction**
  - **Signature** :
    ```php
    ActionResult performAction(ActionRequest $request)
    ```
  - **Input** :
    - `actionId` (string)
    - `actorData` et `targetData` (JSON, fields métiers)
    - `rollResult` (int ou structure plus détaillée)
    - `params` (options spécifiques)
  - **Output** :
    - `effects` (liste de modifications à appliquer sur le `context`)
    - `eventsToTrigger` (liste d’événements)

- **resolveTurn**
  - **Signature** :
    ```php
    TurnResult resolveTurn(TurnRequest $request)
    ```
  - **Input** :
    - `turnData` (liste d’`ActionRequest` séquencés et données de contexte)
  - **Output** :
    - `turnEffects` (agrégation d’`effects`)
    - `pendingReactions` (liste de `ActionRequest` à réexécuter)

### 2.3. Contraintes

- **Stateless** : toute information d’état doit être passée en entrée et reçue en sortie.
- **Découplé** : ne pas importer de classes de persistance, de dés ou de user/domain.
- **Performance** : appel synchrone < 10 ms pour exécuter une action simple.

### 2.4. Technologies & Architecture

- **Langage** : PHP 8.1+, typage strict
- **Style** : PSR-12, SOLID
- **Gestion des dépendances** : Composer, auto-wiring pour DI
- **Tests** : PHPUnit, couverture > 90 % pour chaque module métier
- **CI/CD** : pipeline GitLab/GitHub Actions (tests + PHPStan)

---

## 3. Backlog Scrum : User Stories & Tâches (Backend Action Logic)

Chaque story exclut le lancer de dés, la persistance et les entités personnages.

### Epic A – Core Action Processing

**US-A1** : En tant que `ActionProcessor`, je veux traiter un `ActionRequest` et renvoyer un `ActionResult`, afin d’exécuter la logique métier d’une action.
- **Tâches :**
  - A1.1 : Définir DTO `ActionRequest`, `ActionResult`.
  - A1.2 : Implémenter `ActionFactory` (Factory Method).
  - A1.3 : Implémenter `ActionProcessor` (Strategy).
  - A1.4 : Tests unitaires pour un cas d’attaque simple.

**US-A2** : En tant que validateur métier, je veux vérifier les règles d’exécution avant `ActionProcessor`, afin de prévenir les invocations invalides.
- **Tâches :**
  - A2.1 : Implémenter `RuleValidator` (Chain of Responsibility).
  - A2.2 : Créer tests pour portée invalide, action déjà consommée.

### Epic B – Tour de Combat

**US-B1** : En tant que `CombatOrchestrator`, je veux ordonner et exécuter une liste d’`ActionRequest` dans un tour, afin de restituer l’ensemble des `TurnResult`.
- **Tâches :**
  - B1.1 : Définir `TurnRequest` et `TurnResult` DTOs.
  - B1.2 : Implémenter méthode `resolveTurn()` (séquençage).
  - B1.3 : Tests d’intégration simulant deux actions enchaînées.

### Epic C – Réactions Automatiques

**US-C1** : En tant que `ReactionHandler`, je veux écouter les `eventsToTrigger` et générer de nouvelles `ActionRequest`, afin de gérer les réactions.
- **Tâches :**
  - C1.1 : Implémenter pattern Observer pour souscrire à un `EventBus`.
  - C1.2 : Mapper chaque type d’événement à une `ReactionAction` (Factory).
  - C1.3 : Tests de réaction à un `onMove` déclenchant opportunité.

### Epic D – Documentation & Qualité

**US-D1** : En tant que mainteneur, je veux une documentation complète des classes et un schéma d’architecture, afin de faciliter l’intégration.
- **Tâches :**
  - D1.1 : Rédiger UML de classes principales et séquences.
  - D1.2 : Générer documentation PHPDoc + README.

**US-D2** : En tant que responsable qualité, je veux une couverture de tests ≥ 90 % et analyse statique verte, pour assurer la robustesse.
- **Tâches :**
  - D2.1 : Configurer PHPStan et fixer les erreurs.
  - D2.2 : Ajouter tests sur chaque module (Processor, Validator, ReactionHandler).

---

## 4. Patrons de conception recommandés

| Module             | Pattern(s)                   | Usage                                     |
|--------------------|------------------------------|-------------------------------------------|
| ActionFactory      | Factory Method               | Instanciation polymorphe d’`Action`       |
| ActionProcessor    | Strategy                     | Injection des comportements d’`execute()` |
| RuleValidator      | Chain of Responsibility      | Validation séquentielle des règles        |
| ReactionHandler    | Observer / Pub-Sub           | Souscription et notification d’événements |
| CombatOrchestrator | Template Method              | Enchaînement standardisé des phases       |
| EventBus           | Singleton                    | Point unique de distribution d’événements |

*Fin de la version ajustée.*  
