<?php
// Définir le chemin vers la base de données SQLite
$bdd_fichier = 'Cluedo_BaseDeDonne.db';

// Charger la base de données SQLite
$sqlite = new SQLite3($bdd_fichier);

// Démarrer la session pour accéder aux variables de session
session_start(); 

// Récupérer le nom du personnage sélectionné
if (isset($_SESSION['nom_personnage'])) {
    $nom_personnage = $_SESSION['nom_personnage']; 
} else {
    $nom_personnage = "Personnage non sélectionné"; 
}

// Vérifier si les informations nécessaires sont disponibles
if (isset($_SESSION['piece'], $_SESSION['personnage_tire'], $_SESSION['arme_tiree'])) {
    $piece = $_SESSION['piece'];               // Pièce sélectionnée
    $personnage = $_SESSION['personnage_tire']; // Personnage tiré
    $arme = $_SESSION['arme_tiree'];           // Arme tirée
} else {
    $message = "Aucune information disponible. Veuillez revenir à la page précédente.";
}

// Initialiser le compteur d'hypothèses
$compteur = isset($_SESSION['compteurChoixSalle']) ? $_SESSION['compteurChoixSalle'] : 1;

// Récupérer les noms des éléments depuis $_SESSION
$piece_nom = $_SESSION['piece'] ?? null;
$personnage_nom = $_SESSION['personnage_tire'] ?? null;
$arme_nom = $_SESSION['arme_tiree'] ?? null;

// Initialiser les IDs correspondants
$piece_id = null;
$personnage_id = null;
$arme_id = null;

// Fonction pour récupérer un ID à partir d'un nom dans une table donnée
function getIdByName($sqlite, $table, $name_column, $id_column, $name) {
    $name = SQLite3::escapeString($name); // Échapper les caractères spéciaux pour éviter les injections SQL
    $query = "SELECT $id_column FROM $table WHERE $name_column = '$name'";
    return $sqlite->querySingle($query); // Retourner l'ID correspondant
}

// Récupérer les IDs des éléments (pièce, personnage, arme)
if ($piece_nom) {
    $piece_id = getIdByName($sqlite, 'pieces', 'nom_piece', 'id_piece', $piece_nom);
}
if ($personnage_nom) {
    $personnage_id = getIdByName($sqlite, 'personnages', 'nom_personnage', 'id_personnage', $personnage_nom);
}
if ($arme_nom) {
    $arme_id = getIdByName($sqlite, 'armes', 'nom_arme', 'id_arme', $arme_nom);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Cluedo - Fin</title>
  <!-- Lien vers la feuille de style -->
  <link rel="stylesheet" href="Style-Fin.css">
  <!-- Icône de la page -->
  <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
</head>
<body>
  <!-- Conteneur principal pour afficher les résultats -->
  <div class="result">
    <div id="message">
      <!-- Titre du message -->
      <h5><u>Bravo tu as gagné !</u></h5>
      <br>
      <h5>La solution était :</h5>

      <!-- Conteneur pour la solution -->
      <div class="solution-container">
        <!-- Affichage de la pièce -->
        <div class="solution-item">
          <h6 class="section-title">Pièce :</h6>
          <img src="<?php echo "img/PieceDuManoir/" . htmlspecialchars($piece_id ?? 'default') . ".webp"; ?>" 
               alt="<?php echo htmlspecialchars($piece_nom); ?>" class="solution-image">
          <p><?php echo htmlspecialchars($piece_nom); ?></p>
        </div>

        <!-- Affichage du personnage -->
        <div class="solution-item">
          <h6 class="section-title">Personnage :</h6>
          <img src="<?php echo "img/personnages/" . htmlspecialchars($personnage_id ?? 'default') . ".webp"; ?>" 
               alt="<?php echo htmlspecialchars($personnage_nom); ?>" class="solution-image">
          <p><?php echo htmlspecialchars($personnage_nom); ?></p>
        </div>

        <!-- Affichage de l'arme -->
        <div class="solution-item">
          <h6 class="section-title">Arme :</h6>
          <img src="<?php echo "img/armes/" . htmlspecialchars($arme_id ?? 'default') . ".webp"; ?>" 
               alt="<?php echo htmlspecialchars($arme_nom); ?>" class="solution-image">
          <p><?php echo htmlspecialchars($arme_nom); ?></p>
        </div>
      </div>
      <br>
      <!-- Statistiques de la partie -->
      <p>
        Tu as visité <?php echo htmlspecialchars($compteur); ?> pièce(s) et fait <?php echo htmlspecialchars($compteur); ?> hypothèse(s).
      </p>
      <br>
      <!-- Boutons pour rejouer ou quitter -->
      <button id="rejouer" onclick="window.location.href='Personnage.php';">Rejouer</button>
      &nbsp;
      <button id="quitter" onclick="window.location.href='Pagedaccueil.php';">Quitter</button>
    </div>
  </div>
</body>
</html>