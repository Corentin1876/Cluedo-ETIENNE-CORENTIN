<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Cluedo - Accueil</title>
  <!-- Lien vers la feuille de style CSS -->
  <link rel="stylesheet" href="Style-accueil.css">
  <!-- Icône de la page (favicon) -->
  <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
</head>

<body>
  <!-- Introduction au jeu -->
  <div class="introduction">
    <p class="TexteIntro">
      Bienvenue dans le monde de Cluedo ! En tant que détective, votre mission est de résoudre un mystère complexe 
      en découvrant l'identité du coupable, l'arme du crime et le lieu de l'infraction. 
      Serez-vous capable de rassembler les indices et de résoudre l'affaire ?
    </p>
  </div>

  <!-- Boîte principale avec le titre du jeu et un bouton pour commencer -->
  <div class="boite">
    <!-- En-tête contenant le titre, un slogan et le logo -->
    <div class="en-tete">
      <h1>Cluedo</h1>
      <p>Résous le mystère</p>
      <img class="logo" src="img/autre/Logo.png" alt="Logo de Cluedo">
    </div>

    <!-- Bouton pour démarrer le jeu -->
    <div class="bouton-jouer">
      <button onclick="demarrerJeu()">Jouer</button>
    </div>
  </div>

  <!-- Section des options supplémentaires -->
  <div class="contenu">
    <!-- Bouton pour accéder aux règles de base -->
    <button onclick="afficherRegles()">Règles de Base</button>
    &nbsp; 
    <!-- Bouton pour accéder au manuel d'utilisation -->
    <button onclick="Unmanueldutilisation()">Manuel d’utilisation</button>
  </div>

  <!-- Pied de page avec une signature -->
  <footer class="footer">
    <p>Site créé par ETIENNE Corentin en SIO1</p>
  </footer>

  <!-- Script JavaScript pour les actions des boutons -->
  <script>
    // Redirige l'utilisateur vers la page du choix des personnages
    function demarrerJeu() {
      window.location.href = "Personnage.php";
    }

    // Redirige l'utilisateur vers la page des règles
    function afficherRegles() {
      window.location.href = "regles.php";
    }

    // Redirige l'utilisateur vers le manuel d'utilisation
    function Unmanueldutilisation() {
      window.location.href = "ManuelUtilisation.php";
    }
  </script>
</body>
</html>