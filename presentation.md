
# 🛡️ Projet Backend D&D 5e

## 🎯 Objectif du projet

Modéliser la logique métier d’un jeu de rôle Donjons & Dragons 5e.

- Gérer les actions (attaque, déplacement, repos, objets…)
- Utiliser des fichiers JSON comme source de données
- Propagation des effets via un système d’événements

---

## 🧱 Architecture orientée objet

- Contrôleur central : `ActionController`
- Usine d’action : `ActionFactory`
- Actions : `AttackAction`, `MoveAction`, `Repos`, etc.
- Modèle métier : `Character`
- Événements : `EventDispatcher`, `Event`, `Listeners`

---

## 🧩 Strategy – `ActionInterface`

Chaque action implémente `ActionInterface` :

- `AttackAction`
- `MoveAction`
- `UseItemAction`

➡️ Comportements encapsulés pour chaque action

---

## 🏭 Factory – `ActionFactory`

Crée dynamiquement l’action à partir du type :

- `"attack"` → `AttackAction`
- `"move"` → `MoveAction`

➡️ Centralise la logique de création  
➡️ Simplifie l’ajout de nouveaux types d’actions

---

## 🔔 Observer – `EventDispatcher`

- Chaque action peut déclencher des événements
- `Dispatcher` notifie tous les `EventListener` enregistrés
- Exemple : `FileLoggerListener` écrit dans un fichier

➡️ Faible couplage entre effet et réaction

---

## 🧠 Responsabilités bien séparées

- `ActionController` : traite les requêtes
- `ActionFactory` : instancie l’action
- `Character` : gère les données d’un personnage
- `EventDispatcher` : diffuse les événements

---

## ⚔️ Exemple : attaque

1. `ActionController` reçoit la requête  
2. `ActionFactory` crée `AttackAction`  
3. `AttackAction` agit sur `Character`  
4. Des `Event` sont générés  
5. `EventDispatcher` les diffuse aux listeners

---

## 📂 Données utilisées

- Personnages stockés en fichiers JSON
- Exemple : `Goblin.json`, `Aragorn.json`
- Contiennent : statistiques, inventaire, sorts

➡️ Pas de base de données utilisée

---

## ⚔️ Exemple d'exécution : attaque

1. **Contrôleur** reçoit une requête JSON  
   → crée une `Action` via la `ActionFactory`

2. **ValidationHandler** vérifie que tout est conforme  
   ✔️ portée, cible valide, ressources suffisantes…

3. **ExecutionHandler** applique les effets de l'action  
   🎯 dégâts, changement d’état, mise à jour du personnage

4. Un ou plusieurs **Event** sont générés  
   📝 exemple : `DamageDealtEvent`, `ItemUsedEvent`

5. Le **EventDispatcher** diffuse ces événements  
   → à tous les `EventListener` enregistrés  
   ✅ log dans un fichier (`FileLoggerListener`), mise à jour UI, etc.
---

## 🚧 Limitations

- ❌ Pas de tests unitaires

---

## 🚀 Prochaines étapes

- Ajouter un système de combat complet
  - Gérer les tours, initiative, actions multiples…
- Ajouter des événements complexes (initiative, mort…)

---

## ✅ Conclusion

Un backend modulaire et extensible :

- Basé sur des patrons solides : Strategy, Factory, Observer
- Données JSON simples à manipuler
- Prêt à évoluer vers des mécaniques plus riches
