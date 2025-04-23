<?php

namespace App\Models\Spells;

abstract class Spell
{
//    Sorts de dégâts : Par exemple, Bouclier de feu ou Éclat de feu.
//    Sorts de soins : Par exemple, Soins ou Guérison divine.
//    Sorts de contrôle : Par exemple, Entrave ou Nuage empoisonné.
//    Sorts utilitaires : Par exemple, Lumière ou Téléportation.
    private string $type;
    private string $name;

    //certaine classe n'ont pas besoin de caster
    private int $castTime;
    private int $restTime;
    private int $level;

    private int $duration;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCastTime(): int
    {
        return $this->castTime;
    }

    public function setCastTime(int $castTime): void
    {
        $this->castTime = $castTime;
    }

    public function getRestTime(): int
    {
        return $this->restTime;
    }

    public function setRestTime(int $restTime): void
    {
        $this->restTime = $restTime;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }


    //b. Composants des Sorts
    //Les sorts peuvent avoir différents types de composants nécessaires pour être lancés :
    //Verbal (V) : Le sort nécessite une incantation verbale.
    //Somatique (S) : Le sort nécessite des gestes spécifiques.
    //Matériel (M) : Le sort nécessite un composant matériel, comme un petit objet ou une substance précieuse. Si le composant est consommé par le sort, il doit être remplacé.
    //Certaines classes, comme les sorciers, peuvent se passer de composants matériels pour certains sorts grâce à un trait spécial.

    //3. Composants de la Magie :
    //Les sorts sont associés à des écoles de magie, qui dictent le type de magie utilisé :
    //Évocation (dégâts directs, comme les boules de feu),
    //Illusion (création d'illusions),
    //Nécromancie (magie des morts et des énergies vitales),
    //Transmutation (changements dans la matière ou les capacités),
    //Conjuration (invocation de créatures ou d'objets),
    //Divination (prédictions, visions, etc.),
    //Abjuration (protection, boucliers magiques),
    //Enchantement (influences sur l'esprit).
}