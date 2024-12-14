<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Cluedo - Manuel d'Utilisation</title>
  <!-- Lien vers la feuille de style CSS -->
  <link rel="stylesheet" href="Style-ManuelUtilisation.css">
  <!-- Icône de la page (favicon) -->
  <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
</head>

<body>
  <!-- Conteneur principal pour le manuel d'utilisation -->
  <div class="conteneur-manuel">
    <!-- Titre principal -->
    <h1>Manuel d'Utilisation</h1>

    <!-- Contenu du manuel -->
    <div class="manual">
      <!-- Introduction au manuel -->
      <p>
        Bienvenue dans le manuel d'utilisation du jeu Cluedo. Voici les instructions pour jouer :
      </p>

      <!-- Instructions pour démarrer une partie -->
      <p>
        Pour commencer une partie, il faut choisir un personnage. Pour cela, cliquez sur le nom du personnage que vous souhaitez sélectionner. 
        Une nouvelle page s'affichera pour confirmer votre choix. Ensuite, cliquez sur "Suivant" pour débuter le jeu.
      </p>

      <!-- Instructions pour formuler une hypothèse -->
      <p>
        Pendant la partie, vous devez formuler une hypothèse. Cela consiste à choisir un personnage suspect, une salle, et une arme. 
        Cliquez sur un élément dans chaque section (Personnage, Salle, Arme). Vous devez obligatoirement sélectionner un élément par section, 
        sinon un message vous demandera de compléter votre choix.
      </p>

      <!-- Confirmation et résultats des hypothèses -->
      <p>
        Une fois vos choix effectués, cliquez sur le bouton "Confirmer l'hypothèse".
        <ul>
          <li>
            Si votre hypothèse est correcte, une page de fin affichera les statistiques de votre partie 
            (nombre de pièces visitées, hypothèses réalisées, etc.).
          </li>
          <li>
            Si votre hypothèse est incorrecte, un récapitulatif de vos choix s'affichera avec une indication sur l'élément erroné.
          </li>
        </ul>
      </p>

      <!-- Processus en cas d'hypothèse incorrecte -->
      <p>
        Après une hypothèse incorrecte, cliquez sur "Suivant" pour choisir une nouvelle salle. Dans cette salle, refaites une hypothèse en 
        suivant le même processus jusqu'à ce que vous trouviez la bonne combinaison.
      </p>

      <!-- Options supplémentaires disponibles pendant le jeu -->
      <p>
        Options supplémentaires du jeu :
        <ul>
          <li>
            À droite de l'écran, un panneau d'informations est disponible. Ce panneau affiche le personnage choisi, 
            la salle actuelle et l'historique des éléments incorrects. Vous pouvez réduire le panneau en cliquant sur "Réduire" 
            et le réouvrir en cliquant sur "Développer". Ce panneau vous aide à suivre vos choix et avancer dans l'enquête.
          </li>
          <li>
            En haut à gauche, une petite image du plan du manoir est visible. Cliquez dessus pour afficher le plan en grand. 
            Vous pouvez fermer le plan en cliquant sur "Fermer l'image".
          </li>
          <li>
            En bas à droite, un bloc-notes est accessible. Cliquez dessus pour l’ouvrir et écrire des notes. Cliquez sur "Fermer" 
            pour refermer le bloc-notes. Les notes sont sauvegardées automatiquement et restent disponibles après actualisation ou 
            changement de page.
          </li>
        </ul>
      </p>

      <!-- Conclusion -->
      <p>
        Avec ces instructions, vous avez tout ce qu'il faut pour jouer à Cluedo. Bonne enquête !
      </p>
    </div>

    <!-- Bouton pour revenir à la page d'accueil -->
    <button onclick="revenirAccueil()">Retour à l'accueil</button>
  </div>

  <!-- Script JavaScript pour gérer la redirection -->
  <script>
    // Fonction pour rediriger l'utilisateur vers la page d'accueil
    function revenirAccueil() {
      window.location.href = "Pagedaccueil.php";
    }
  </script>
</body>
</html>