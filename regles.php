<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Cluedo - Règles</title>
  <!-- Lien vers la feuille de style CSS -->
  <link rel="stylesheet" href="Style-regles.css">
  <!-- Icône de la page (favicon) -->
  <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
</head>

<body>
  <!-- Conteneur principal pour les règles -->
  <div class="conteneur-regles">
    <!-- Titre principal des règles -->
    <h1>Règles du Cluedo</h1>

    <!-- Explications des règles du jeu -->
    <p>
      Le but du jeu est de découvrir qui a commis le meurtre, avec quelle arme, et dans quelle pièce du manoir. 
      Si le joueur devine correctement ces trois éléments alors il gagne la partie.
    </p>

    <p>
      Au début de la partie, le joueur choisit un personnage. Le jeu sélectionne alors, de manière aléatoire et secrète, 
      un personnage, une arme et une pièce. Le joueur commence dans le Hall du manoir.
    </p>

    <p>
      Le joueur peut se déplacer de pièce en pièce en utilisant les portes qui relient les salles adjacentes.
    </p>

    <p>
      Dans chaque pièce, le joueur peut formuler une hypothèse en choisissant un personnage suspect, une arme, et la pièce 
      où il pense que le crime a eu lieu. Cependant, il n'est pas possible de formuler deux hypothèses consécutives dans 
      la même pièce.
    </p>

    <p>
      Si l'hypothèse du joueur est correcte, il gagne immédiatement la partie. Si elle est incorrecte, un des éléments 
      erronés lui est révélé pour l'aider à affiner ses déductions.
    </p>

    <p>
      À la fin de la partie, le joueur a gagné et la page lui affiche la combinaison correcte, le nombre de pièces visitées 
      et le nombre d'hypothèses réalisées. Le joueur peut alors choisir de recommencer une nouvelle partie.
    </p>

    <!-- Bouton pour revenir à la page d'accueil -->
    <button onclick="revenir()">Page d'accueil</button>
  </div>

  <!-- Script JavaScript pour gérer la redirection -->
  <script>
    // Fonction pour rediriger l'utilisateur vers la page d'accueil
    function revenir() {
      window.location.href = "Pagedaccueil.php";
    }
  </script>
</body>
</html>