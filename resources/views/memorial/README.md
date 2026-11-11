# Ressources de l’hommage

Le composant `x-memorial-flower` utilise `public/images/memorial/fleur.png`, `.webp` ou `.svg` dès qu’un de ces fichiers existe; sinon il affiche le SVG provisoire. Remplacer/ajouter une image à cet emplacement permet de changer le dessin sans modifier la logique.

Les agrégats visibles à la vue chef ne contiennent aucun identifiant nominatif. La clé étrangère `memorial_offerings.user_id` supprime en cascade les fleurs lorsqu’un compte est supprimé.
