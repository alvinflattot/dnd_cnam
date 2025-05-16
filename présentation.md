## mvp V1.0
- ActionFactory (Factory Method) instancie dynamiquement l’Action adaptée à sa configuration.
- ActionProcessor (Strategy) injecte et exécute la logique métier spécifique de chaque action.
- RuleValidator (Chain of Responsibility) valide séquentiellement toutes les préconditions métier.

## v2.0
- ReactionHandler (Observer/Publisher–Subscriber) déclenche automatiquement les réactions aux événements de combat.
- CombatOrchestrator (Template Method) enchaîne et orchestre les phases de tour ; EventBus (Singleton) centralise la diffusion des événements.