<?php

// Définition de la base de données et initialisation de la session
$bdd_fichier = 'Cluedo_BaseDeDonne.db';

// Démarrer la session PHP
session_start(); 

// Vérifier si une pièce actuelle est définie dans la session
if (isset($_SESSION['pieceActuelle'])) {
    $pieceActuelle = $_SESSION['pieceActuelle'];
} else {
    $pieceActuelle = "Valeur par défaut"; 
}

// Connexion à la base de données SQLite
$sqlite = new SQLite3($bdd_fichier);

// Requête SQL pour récupérer les pièces adjacentes à la pièce actuelle
$sql = 'SELECT DISTINCT adj.id_piece, adj.nom_piece
        FROM pieces 
        INNER JOIN portes AS p1 ON p1.id_piece1 = pieces.id_piece OR p1.id_piece2 = pieces.id_piece
        INNER JOIN pieces AS adj ON (adj.id_piece = p1.id_piece1 OR adj.id_piece = p1.id_piece2)
        WHERE pieces.nom_piece = :piece 
        AND adj.nom_piece != pieces.nom_piece';

// Préparer et exécuter la requête SQL
$requete = $sqlite->prepare($sql);  
$requete->bindValue(':piece', $pieceActuelle, SQLITE3_TEXT);
$result = $requete->execute();

// Fonction pour générer le chemin d'une image en fonction de l'ID et du type
function getImagePath($type, $id) {
    $basePath = 'img/' . $type . '/';
    return $basePath . $id . '.webp';
}

// Récupérer les personnages depuis la base de données
$personnages = [];
$persoResult = $sqlite->query('SELECT id_personnage, nom_personnage FROM personnages');
while ($row = $persoResult->fetchArray(SQLITE3_ASSOC)) {
    $personnages[$row['nom_personnage']] = $row['id_personnage'];
}

// Récupérer les armes depuis la base de données
$armes = [];
$armeResult = $sqlite->query('SELECT id_arme, nom_arme FROM armes');
while ($row = $armeResult->fetchArray(SQLITE3_ASSOC)) {
    $armes[$row['nom_arme']] = $row['id_arme'];
}

// Vérifier si un personnage est sélectionné dans la session
if (isset($_SESSION['nom_personnage'])) {
    $personnage_selectioner = $_SESSION['nom_personnage']; 
} else {
    $personnage_selectioner = "Personnage non sélectionné"; 
}

// Vérifier si des informations sur la pièce, le personnage et l'arme sont stockées dans la session
if (isset($_SESSION['piece'], $_SESSION['personnage_tire'], $_SESSION['arme_tiree'])) {
    $piece = $_SESSION['piece'];               
    $personnage = $_SESSION['personnage_tire']; 
    $arme = $_SESSION['arme_tiree'];            
} else {
    $message = "Aucune information disponible. Veuillez revenir à la page précédente.";
}

// Gestion des éléments incorrects
if (isset($_SESSION['incorrectElement'])) {
    $incorrectElementBase = $_SESSION['incorrectElement'];


    if (!isset($_SESSION['incorrectElementList'])) {
        $_SESSION['incorrectElementList'] = []; 
    }

    $_SESSION['incorrectElementList'][] = $incorrectElementBase; 
}

// Si un élément incorrect a été détecté, ajouter à la liste si ce n'est pas déjà fait
if (isset($_SESSION['incorrectElement'])) {
    $incorrectElementBase = $_SESSION['incorrectElement'];

    // Initialiser la liste des éléments incorrects si nécessaire
    if (!isset($_SESSION['incorrectElementList'])) {
        $_SESSION['incorrectElementList'] = [];
    }

    // Ajouter l'élément à la liste s'il n'est pas déjà présent
    if (!in_array($incorrectElementBase, $_SESSION['incorrectElementList'])) {
        $_SESSION['incorrectElementList'][] = $incorrectElementBase;
    }

    // Supprimer l'élément incorrect après l'avoir ajouté
    unset($_SESSION['incorrectElement']);
}

// Initialiser un compteur de choix de salle
if (!isset($_SESSION['compteurChoixSalle'])) {
    $_SESSION['compteurChoixSalle'] = 1; 
}

// Vérification du formulaire de choix de salle (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choixSalle'])) {
    $_SESSION['compteurChoixSalle']++; 
    
    $destination = $Page_salles[$_POST['choixSalle']] ?? ''; 
    if (!empty($destination)) {
        header('Location: ' . $destination); 
        exit(); 
    }
}

// Récupérer l'ID de la pièce actuelle
$sql = 'SELECT id_piece FROM pieces WHERE nom_piece = :piece';
$requeteIdPiece = $sqlite->prepare($sql);
$requeteIdPiece->bindValue(':piece', $pieceActuelle, SQLITE3_TEXT);
$resultIdPiece = $requeteIdPiece->execute();
$pieceActuelleId = $resultIdPiece->fetchArray(SQLITE3_ASSOC)['id_piece'] ?? null;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cluedo - Jeu - <?php echo htmlspecialchars($pieceActuelle); ?></title>
    <link rel="stylesheet" href="Style-Salle-Choix.css">
    <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">

    <style>
body {
    background-image: url('<?php echo "img/PieceDuManoir/" . htmlspecialchars($pieceActuelleId ?? 'default') . ".webp"; ?>');
}
</style>

</head>
<body onload="showInfoPanel()">

<!-- Affichage de l'image du plan du manoir -->
<img src="img\autre\Plan_Manoir.png" alt="Afficher l'image" class="image-thumbnail" id="showButton4">

<div class="overlay4" id="overlay4"></div>

<div class="image-modal4" id="imageModal4">
    <button class="button4" id="closeButton4">Fermer l'image</button>
    <img src="img\autre\Plan_Manoir.png" alt="Image Exemple">
</div>

<script>
    // Script pour afficher/fermer le modal de l'image du plan du manoir
    const showButton4 = document.getElementById('showButton4');
    const closeButton4 = document.getElementById('closeButton4');
    const imageModal4 = document.getElementById('imageModal4');
    const overlay4 = document.getElementById('overlay4');


    showButton4.addEventListener('click', function() {
        imageModal4.style.display = 'block'; 
        overlay4.style.display = 'block';    
        showButton4.style.display = 'none'; 
        closeButton4.style.display = 'block'; 
    });


    closeButton4.addEventListener('click', function() {
        imageModal4.style.display = 'none';  
        overlay4.style.display = 'none';     
        showButton4.style.display = 'block'; 
        closeButton4.style.display = 'none'; 
    });


    overlay4.addEventListener('click', function() {
        imageModal4.style.display = 'none';  
        overlay4.style.display = 'none';     
        showButton4.style.display = 'block'; 
        closeButton4.style.display = 'none'; 
    });
</script>

<!-- En-tête de la page -->
<header id="titre-header">
    <h1 class="titre"><?php echo htmlspecialchars($pieceActuelle); ?></h1>
    <div class="choix-container">
        <h2 class="sous-titre">Quelle salle voulez-vous aller ?</h2>
    </div>
    <br><br><br><br><br><br><br><br>
</header>

<!-- Conteneur des options de salles -->
<div class="container">
    <?php
    // Afficher les salles adjacentes à la pièce actuelle
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $nom_piece = $row['nom_piece'];
        $id_piece = $row['id_piece']; // Récupérer l'ID de la salle
        
        // Utiliser la fonction pour générer le chemin de l'image
        $imagePath = getImagePath('PieceDuManoir', $id_piece);

        if (isset($nom_piece)) {
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($nom_piece) . '">';
            echo '<form action="" method="POST">'; // Requêtes POST sur la même page
            echo '<button type="submit" name="choixSalle" value="' . htmlspecialchars($nom_piece) . '">';
            echo htmlspecialchars($nom_piece);
            echo '</button>';
            echo '</form>';
            echo '</div>';
        }
    }

// Gestion POST pour incrémenter et rediriger
if (isset($_POST['choixSalle'])) {
     // Incrémenter le compteur
    $choixSalle = $_POST['choixSalle'];
    $_SESSION['pieceActuelle'] = $choixSalle;
    // Rediriger vers Hall.php de manière forcée
    header('Location: Salles.php'); // Redirection vers Hall.php
    exit(); // Fin du script après la redirection
}
    ?>
</div>

<?php

//récupération de l'ID du personnage en fonction du nom
$sql = 'SELECT id_personnage FROM personnages WHERE nom_personnage = :nom_personnage';
$requete = $sqlite->prepare($sql);
$requete->bindValue(':nom_personnage', $personnage_selectioner, SQLITE3_TEXT);
$result = $requete->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row) {
    $personnage_selectioner = $row['id_personnage'];  // On récupère l'ID
}

?>

<!-- Panneau d'informations à droite -->
<div class="info-panel" id="info-panel">
    <button id="toggle-button" onclick="togglePanel()">Réduire</button>

    <div class="personnage-section">
    <h4>Personnage choisi :</h4>
    <?php
    // Si un personnage a été sélectionné dans la session
    if (isset($personnage_selectioner) && $personnage_selectioner != "Personnage non sélectionné") {
        // Récupérer le nom du personnage à partir de l'ID
        $sql = 'SELECT nom_personnage FROM personnages WHERE id_personnage = :id_personnage';
        $requeteNomPersonnage = $sqlite->prepare($sql);
        $requeteNomPersonnage->bindValue(':id_personnage', $personnage_selectioner, SQLITE3_INTEGER);
        $resultNomPersonnage = $requeteNomPersonnage->execute();
        $row = $resultNomPersonnage->fetchArray(SQLITE3_ASSOC);

        // Vérifier si on a trouvé un nom correspondant à l'ID
        if ($row) {
            $nom_personnage = $row['nom_personnage'];
        } else {
            $nom_personnage = "Nom inconnu";  // En cas de problème
        }
        
        // Afficher l'image et le nom du personnage
        echo '<img src="img/personnages/' . $personnage_selectioner . '.webp" 
             alt="Image de ' . htmlspecialchars($nom_personnage) . '" 
             class="personnage-image">';
        echo '<p>' . htmlspecialchars($nom_personnage) . '</p>';
    } else {
        echo '<p>Pas encore choisi</p>';
    }
    ?>
</div>

    <hr class="divider">

    <div class="piece-section">
        <h4>Pièce actuelle :</h4>
        <img src="<?php echo "img/PieceDuManoir/" . htmlspecialchars($pieceActuelleId ?? 'default') . ".webp"; ?>" alt="Image de la pièce" class="piece-image">
        <p><?php echo htmlspecialchars($pieceActuelle); ?></p>
    </div>

    <hr class="divider">

    <!-- Historique des hypothèses -->
    <div class="historique-section">
        <h4>Historique des hypothèses fausses :</h4>
        <ul id="historique-hypotheses">
            <?php if (!empty($_SESSION['incorrectElementList'])): ?>
                <?php foreach ($_SESSION['incorrectElementList'] as $index => $element): ?>
                    <li><?php echo ($index + 1) . " - " . htmlspecialchars($element); ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun élément incorrect pour le moment.</li>
        <?php endif; ?>
        </ul>
    </div>
</div>

<script>
    // Fonction pour afficher ou réduire le panneau d'informations
    function showInfoPanel() {
        document.getElementById("info-panel").classList.add("visible");
    }

    function togglePanel() {
    var panel = document.getElementById("info-panel");
    var toggleButton = document.getElementById("toggle-button");

    if (panel.classList.contains("reduced")) {
        panel.classList.remove("reduced");
        toggleButton.innerText = "Réduire";
    } else {
        panel.classList.add("reduced");
        toggleButton.innerText = "Développer";
    }
}
</script>

<!-- Bloc-Note -->
<img id="note-button6" src="img\autre\BlocNote.png" alt="Ouvrir le Bloc-Note">

<div id="overlay6"></div>

<div id="note-container6">
    <button id="close-button6">Fermer</button>
    <textarea id="note-textarea6" placeholder="Écrivez ici..."></textarea>
</div>

<script>
    // Script pour gérer le bloc-note et son affichage
    const noteButton6 = document.getElementById('note-button6');
    const noteContainer6 = document.getElementById('note-container6');
    const overlay6 = document.getElementById('overlay6');
    const closeButton6 = document.getElementById('close-button6');
    const noteTextarea6 = document.getElementById('note-textarea6');

    window.addEventListener('load', function() {
        const savedNote6 = localStorage.getItem('note');
        if (savedNote6) {
            document.getElementById('note-textarea6').value = savedNote6;
        }
    });

    document.getElementById('note-textarea6').addEventListener('input', function() {
        localStorage.setItem('note', this.value);
    });

    noteButton6.addEventListener('click', () => {
        noteContainer6.style.display = 'block';
        overlay6.style.display = 'block';
    });

    closeButton6.addEventListener('click', () => {
        noteContainer6.style.display = 'none';
        overlay6.style.display = 'none';
    });

    overlay6.addEventListener('click', () => {
        noteContainer6.style.display = 'none';
        overlay6.style.display = 'none';
    });
</script>

</body>
</html>
<?php 
  $sqlite->close(); // Fermer la connexion à la base de données
?>