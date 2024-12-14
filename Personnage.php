<?php
// Définir le chemin vers la base de données SQLite
$bdd_fichier = 'Cluedo_BaseDeDonne.db';

// Charger la base de données SQLite
$sqlite = new SQLite3($bdd_fichier);

// Initialiser les variables nécessaires
$personnage = null;
$message = "";

// Récupérer toutes les armes depuis la base de données
$sql = 'SELECT nom_arme FROM armes';
$result_arme = $sqlite->query($sql);

// Stocker les noms des armes dans un tableau
$armes = [];
while ($row = $result_arme->fetchArray(SQLITE3_ASSOC)) {
    $armes[] = $row['nom_arme'];
}

// Gérer le formulaire POST pour le choix du personnage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['personnage'])) {
    $id_personnage = $_POST['personnage']; // ID du personnage choisi

    // Requête pour récupérer les informations du personnage sélectionné
    $sql = 'SELECT nom_personnage, couleur FROM personnages WHERE id_personnage = :id_personnage';
    $requete = $sqlite->prepare($sql);

    if (!$requete) {
        $message = "Erreur dans la requête SQL.";
    } else {
        // Associer l'ID du personnage à la requête préparée
        $requete->bindValue(':id_personnage', $id_personnage, SQLITE3_INTEGER);
        $result = $requete->execute();
        $personnage = $result->fetchArray(SQLITE3_ASSOC);

        if (!$personnage) {
            $message = "Personnage introuvable.";
        } else {
            // Ajouter une image au personnage (si disponible)
            $personnage['image'] = "img/personnages/{$id_personnage}.webp";

            // Tirer une pièce aléatoire
            $sql_piece = 'SELECT nom_piece FROM pieces ORDER BY RANDOM() LIMIT 1';
            $result_piece = $sqlite->query($sql_piece);
            $piece_tiree = $result_piece->fetchArray(SQLITE3_ASSOC)['nom_piece'];

            // Tirer un personnage aléatoire différent du sélectionné
            $sql = 'SELECT nom_personnage FROM personnages WHERE id_personnage != :id_personnage ORDER BY RANDOM() LIMIT 1';
            $requete_personnage = $sqlite->prepare($sql);
            $requete_personnage->bindValue(':id_personnage', $id_personnage, SQLITE3_INTEGER);
            $result_personnage = $requete_personnage->execute();
            $personnage_tire = $result_personnage->fetchArray(SQLITE3_ASSOC)['nom_personnage'];

            // Tirer une arme aléatoire
            $resultat_arme = $armes[array_rand($armes)];

            // Démarrer la session et sauvegarder les données
            session_start();
            $_SESSION['nom_personnage'] = $personnage['nom_personnage'];
            $_SESSION['personnage_image'] = $personnage['image'];
            $_SESSION['piece'] = $piece_tiree;
            $_SESSION['personnage_tire'] = $personnage_tire;
            $_SESSION['arme_tiree'] = $resultat_arme;
        }
    }
}

// Initialiser les éléments incorrects si non définis
if (!isset($_SESSION['incorrectElementList'])) {
    $_SESSION['incorrectElementList'] = [];
}

// Initialiser le compteur de choix de salle
$_SESSION['compteurChoixSalle'] = 1;

// Définir la pièce actuelle par défaut
$_SESSION['pieceActuelle'] = 'Hall';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cluedo - Personnage</title>
    <link rel="stylesheet" href="Style-Personnage.css">
    <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
</head>
<body>
    <!-- Si un personnage a été sélectionné -->
    <?php if ($personnage): ?>
        <div class="result">
            <div id="personnage-message">
                <h2>Vous avez choisi : <?php echo htmlspecialchars($personnage['nom_personnage']); ?></h2>
                <button id="suivant" onclick="window.location.href='Salles.php';">Suivant</button>
            </div>
        </div>
    <?php else: ?>
        <!-- Section de sélection du personnage -->
        <div class="choix-container">
            <h2 class="sous-titre">Choisissez votre personnage</h2>
        </div>
        <div class="button-container">
            <?php
            // Récupérer la liste des personnages depuis la base de données
            $sql = 'SELECT id_personnage, nom_personnage, couleur FROM personnages';
            $liste_personnages = $sqlite->query($sql);

            // Afficher chaque personnage comme un bouton
            while ($row = $liste_personnages->fetchArray(SQLITE3_ASSOC)) {
                $id_personnage = htmlspecialchars($row['id_personnage']);
                $nom_personnage = htmlspecialchars($row['nom_personnage']);
                $couleur = htmlspecialchars($row['couleur']);
                $image_path = "img/personnages/{$id_personnage}.webp";

                echo '<form method="POST" style="display: inline-block; text-align: center;">';
                echo '<img src="' . $image_path . '" alt="' . $nom_personnage . '" class="personnage-image">';
                echo '<button type="submit" name="personnage" value="' . $id_personnage . '" style="border: 3px solid ' . $couleur . '; color: ' . $couleur . ';">' . $nom_personnage . '</button>';
                echo '</form>';
            }
            ?>
        </div>
        <!-- Afficher un message d'erreur si nécessaire -->
        <?php if ($message): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Script JavaScript -->
    <script>
        // Rediriger vers la page des salles
        function redirectToTestPage() {
            window.location.href = "Salles.php";
        }

        // Supprimer le contenu du bloc-notes lors du chargement
        window.onload = function() {
            localStorage.removeItem('note'); 
        }
    </script>
</body>
</html>

<?php 
// Fermer la connexion SQLite
$sqlite->close(); 
?>