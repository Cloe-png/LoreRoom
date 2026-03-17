<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run()
    {
        $jobs = [
            'Agriculteur','Apiculteur','Arboriculteur','Éleveur','Berger','Viticulteur','Vigneron','Maraîcher','Bûcheron','Pêcheur',
            'Boulanger','Pâtissier','Fromager','Charcutier','Boucher','Poissonnier','Traiteur','Cuisinier','Chef','Sommelier',
            'Serveur','Barman','Brasseur','Distillateur','Chocolatier','Confiseur','Glacier','Meunier','Maltier',
            'Forgeron','Armurier','Ferronnier','Orfèvre','Bijoutier','Horloger','Tailleur','Cordonnier','Tanneur','Tisseur',
            'Tisserand','Teinturier','Brodeur','Couturier','Modiste','Sellier','Charpentier','Menuisier','Ébéniste','Maçon',
            'Tailleur de pierre','Couvreur','Zingueur','Plâtrier','Peintre en bâtiment','Verrier','Céramiste','Potier','Pipier',
            'Ingénieur','Architecte','Urbaniste','Géomètre','Cartographe','Arpenteur','Topographe','Scientifique','Physicien','Chimiste',
            'Biologiste','Botaniste','Zoologiste','Astronome','Mathématicien','Statisticien','Inventeur','Technicien','Mécanicien','Électricien',
            'Électronicien','Automaticien','Roboticien','Programmeur','Développeur','Analyste','Data scientist','Testeur','Admin système','DevOps',
            'Sécuritaire informatique','Hacker éthique',
            'Médecin','Chirurgien','Infirmier','Sage-femme','Apothicaire','Herboriste','Pharmacien','Dentiste','Vétérinaire','Psychologue',
            'Psychiatre','Kinésithérapeute','Ostéopathe','Ergothérapeute','Orthophoniste','Ambulancier','Guérisseur','Alchimiste','Nécromancien',
            'Prêtre','Moine','Pasteur','Imam','Rabbin','Chaman','Oracle','Prophète','Exorciste','Missionnaire','Théologien','Archiviste',
            'Historien','Bibliothécaire','Scribe','Copiste','Chroniqueur','Journaliste','Écrivain','Poète','Dramaturge','Scénariste',
            'Acteur','Metteur en scène','Musicien','Chanteur','Compositeur','Danseur','Peintre','Sculpteur','Illustrateur','Graveur',
            'Photographe','Cinéaste','Animateur','Artisan','Designer','Styliste',
            'Militaire','Soldat','Officier','Général','Capitaine','Marin','Pirate','Corsaire','Mercenaire','Chevalier',
            'Garde','Garde du corps','Archer','Piquier','Lancier','Cavalier','Éclaireur','Espion','Assassin','Tueur à gages',
            'Détective','Enquêteur','Policier','Gendarme','Shérif','Juge','Avocat','Procureur','Notaire','Greffier',
            'Maître d’armes','Instructeur','Maître d’entraînement','Gladiateur','Dresseur','Chasseur','Trappeur','Pisteur',
            'Marchand','Négociant','Commerçant','Épicier','Libraire','Antiquaire','Vendeur','Courtier','Banquier','Comptable',
            'Trésorier','Contrôleur','Assureur','Usurier','Changeur',
            'Explorateur','Aventurier','Pionnier','Guide','Cartographe','Pilote','Capitaine de navire','Navigateur','Mécano','Exploitant minier',
            'Mineur','Prospecteur','Géologue','Carrier','Charretier','Cocher','Messager','Postier','Facteur','Coursier',
            'Forain','Saltimbanque','Jongleur','Acrobate','Magicien','Illusionniste','Conteur','Barde',
            'Maire','Gouverneur','Ministre','Conseiller','Diplomate','Ambassadeur','Administrateur','Fonctionnaire','Sénateur','Député',
            'Roi','Reine','Prince','Princesse','Noble','Seigneur','Intendant',
            'Entrepreneur','PDG','Directeur','Manager','Chef de projet','Responsable RH','Recruteur','Formateur','Coach',
            'Gardien','Concierge','Chauffeur','Pilote de course','Mécanicien avion','Capitaine de port','Douanier','Contrôleur',
            'Géant de foire','Pêcheur perlier','Scaphandrier','Plongeur','Explorateur spatial','Astronaute','Ingénieur spatial',
            'Terraformeur','Xénobiologiste','Xénolinguiste','Cybernéticien','Bio-ingénieur','Généticien','Clonologue',
            'Surveillant','Éducateur','Professeur','Instituteur','Recteur','Doyen','Chercheur','Étudiant',
            'Architecte naval','Chantier naval','Charpentier naval','Matelot','Harponeur','Whaler',
            'Gardien de prison','Geôlier','Bourreau','Exécuteur','Révolutionnaire','Saboteur','Résistant',
            'Pompier','Sauveteur','Secouriste','Survivaliste','Maître d’hôtel','Sommelier','Traiteur',
            'Technomancien','Sorcière','Sorcier','Mage','Enchanteur','Évocateur','Invocationniste','Druide','Ranger',
            'Paladin','Clerc','Moine guerrier','Samouraï','Ninja','Ronin',
            'Pilier','Dockeur','Manutentionnaire','Ouvrier','Soudeur','Tourneur','Fraiseur','Usineur',
            'Chasseur de primes','Explorateur de ruines','Archéologue','Paléontologue','Conservateur',
            'Gourou','Influenceur','Orateur','Propagandiste','Stratège','Tacticien',
        ];

        $now = now();
        $rows = collect($jobs)
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->map(fn ($name) => [
                'world_id' => null,
                'name' => $name,
                'description' => null,
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        foreach (array_chunk($rows, 200) as $chunk) {
            Job::upsert($chunk, ['world_id', 'name'], ['is_default', 'updated_at']);
        }
    }
}
