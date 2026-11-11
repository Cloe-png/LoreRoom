<?php

namespace Database\Seeders;

use App\Models\WritingPrompt;
use Illuminate\Database\Seeder;

class WritingPromptSeeder extends Seeder
{
    public function run()
    {
        $rows = [
            ['Que mange {nom} le matin ?', 'character', 'likes'], ['Quelle odeur reconnaît-on à l’entrée de {nom} ?', 'place', 'atmosphere'],
            ['Quelle fête célèbre {nom} chaque année ?', 'any', null], ['Quelle rumeur circule sur {nom} ?', 'any', 'summary'],
            ['De quoi {nom} a-t-il le plus peur ?', 'character', 'psychology_notes'], ['Quel secret protège {nom} ?', 'any', 'secrets'],
            ['Qui a fondé {nom} ?', 'place', 'history'], ['Quelle règle personne n’ose enfreindre chez {nom} ?', 'faction', 'summary'],
            ['Quel signe annonce l’arrivée de {nom} ?', 'species', 'characteristics'], ['Quelle expression revient souvent chez {nom} ?', 'character', 'voice_tics'],
            ['Quel objet ne quitte jamais {nom} ?', 'character', 'likes'], ['Qu’est-ce qui a changé {nom} à jamais ?', 'any', 'history'],
            ['Quel endroit de {nom} est interdit ?', 'place', 'description'], ['Quelle dette pèse sur {nom} ?', 'character', 'secrets'],
            ['Quel métier manque à {nom} ?', 'job', 'description'], ['Quelle légende raconte-t-on à propos de {nom} ?', 'lore', 'content'],
            ['Que devient {nom} à la tombée de la nuit ?', 'any', null], ['Qui est le rival de {nom} ?', 'any', 'relations'],
            ['Quel son caractérise {nom} ?', 'place', 'atmosphere'], ['Quelle coutume étonne les étrangers chez {nom} ?', 'faction', 'summary'],
            ['Quelle qualité distingue {nom} ?', 'species', 'characteristics'], ['Quel événement a donné son nom à {nom} ?', 'lore', 'content'],
            ['Que refuse toujours {nom} ?', 'character', 'psychology_notes'], ['Quelle saison préfère {nom} ?', 'any', null],
            ['Quel voisin redoute {nom} ?', 'place', 'dangers'], ['Quelle promesse lie les membres de {nom} ?', 'faction', 'motto'],
            ['Quel détail rend {nom} reconnaissable ?', 'character', 'appearance_description'], ['Qu’est-ce qui se transmet dans {nom} ?', 'species', 'origin'],
            ['Quel événement a bouleversé {nom} ?', 'event', 'summary'], ['Quelle histoire les enfants entendent-ils sur {nom} ?', 'any', 'summary'],
        ];
        foreach ($rows as [$text, $type, $hint]) WritingPrompt::updateOrCreate(['text' => $text], ['target_type' => $type, 'section_hint' => $hint, 'is_active' => true]);
    }
}
