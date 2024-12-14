<?php

// Déclaration du fichier de base de données SQLite
$bdd_fichier = 'Cluedo_BaseDeDonne.db';

// Démarre ou reprend une session existante
session_start(); 

// Vérifie si une "pieceActuelle" est définie dans la session, sinon définit "Hall" comme pièce par défaut
if (isset($_SESSION['pieceActuelle'])) {
    $pieceActuelle = $_SESSION['pieceActuelle'];
} else {
    $pieceActuelle = "Hall"; 
}

// Crée une instance de SQLite3 pour accéder à la base de données
$sqlite = new SQLite3($bdd_fichier);

// Prépare la requête SQL pour récupérer les pièces adjacentes à la pièce actuelle
$sql = 'SELECT adj.id_piece, adj.nom_piece ';
$sql .= 'FROM pieces INNER JOIN portes ON portes.id_piece1=pieces.id_piece OR portes.id_piece2=pieces.id_piece ';
$sql .= 'INNER JOIN pieces AS adj ON portes.id_piece1=adj.id_piece OR portes.id_piece2=adj.id_piece ';
$sql .= 'WHERE adj.id_piece!=pieces.id_piece AND pieces.nom_piece LIKE :piece';

// Prépare et exécute la requête en liant la valeur de la pièce actuelle
$requete = $sqlite->prepare($sql);  
$requete->bindValue(':piece', $pieceActuelle, SQLITE3_TEXT);
$result = $requete->execute();

// Vérifie si un personnage est sélectionné, sinon affiche "Personnage non sélectionné"
if (isset($_SESSION['nom_personnage'])) {
    $personnage_selectioner = $_SESSION['nom_personnage']; 
} else {
    $personnage_selectioner = "Personnage non sélectionné"; 
}

// Vérifie si des informations sur la pièce, personnage et arme ont été définies dans la session
if (isset($_SESSION['piece'], $_SESSION['personnage_tire'], $_SESSION['arme_tiree'])) {
    $piece = $_SESSION['piece'];               
    $personnage = $_SESSION['personnage_tire']; 
    $arme = $_SESSION['arme_tiree'];           
} else {
    $message = "Aucune information disponible. Veuillez revenir à la page précédente.";
}

// Variables pour les hypothèses
$hypothese_Personnage = '';
$hypothese_Salle = '';
$hypothese_Arme = '';
$confirmation_message = '';

// Si le formulaire est soumis en méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les valeurs envoyées par le formulaire et les stocke dans la session
    if (isset($_POST['personnage'])) {
        $_SESSION['hypothese_personnage'] = $_POST['personnage'];
    }
    if (isset($_POST['salle'])) {
        $_SESSION['hypothese_salle'] = $_POST['salle'];
    }
    if (isset($_POST['arme'])) {
        $_SESSION['hypothese_arme'] = $_POST['arme'];
    }
}

// Récupère l'ID de la pièce actuelle à partir de la base de données
$sql = 'SELECT id_piece FROM pieces WHERE nom_piece = :piece';
$requeteIdPiece = $sqlite->prepare($sql);
$requeteIdPiece->bindValue(':piece', $pieceActuelle, SQLITE3_TEXT);
$resultIdPiece = $requeteIdPiece->execute();
$pieceActuelleId = $resultIdPiece->fetchArray(SQLITE3_ASSOC)['id_piece'] ?? null;

// Récupère l'ID du personnage sélectionné à partir de la base de données
$sql = 'SELECT id_personnage FROM personnages WHERE nom_personnage = :nom_personnage';
$requete = $sqlite->prepare($sql);
$requete->bindValue(':nom_personnage', $personnage_selectioner, SQLITE3_TEXT);
$result = $requete->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

// Si un personnage est trouvé, on met à jour la variable avec son ID
if ($row) {
    $personnage_selectioner = $row['id_personnage'];  
}

// Récupère le nom du personnage sélectionné dans la session
if (isset($_SESSION['nom_personnage'])) {
    $nom_personnage_bdd = $_SESSION['nom_personnage'];
} else {
    $nom_personnage_bdd = null; // Valeur par défaut si aucun personnage n'est sélectionné
}

// Sauvegarde la pièce actuelle dans la session
$_SESSION['pieceActuelle'] = $pieceActuelle;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cluedo - Jeu - <?php echo htmlspecialchars($pieceActuelle); ?></title>
    <link rel="icon" type="image/x-icon" href="img/autre/Logo.png">
    <link rel="stylesheet" href="Style-Salle.css">

    <!-- Dynamique pour afficher l'image de la pièce actuelle -->
    <style>
        body {
            background-image: url('<?php echo "img/PieceDuManoir/" . htmlspecialchars($pieceActuelleId ?? 'default') . ".webp"; ?>');
        }
    </style>

     <!-- Script pour gérer la mise en surbrillance des boutons -->
    <script>
    function toggleHighlight(button, section) {
        const buttons = document.querySelectorAll('.' + section + ' button');
        if (button.classList.contains('highlighted')) {
            button.classList.remove('highlighted');
        } else {  
            buttons.forEach(function(btn) {
                btn.classList.remove('highlighted');
            });
            button.classList.add('highlighted');
        }
    }

    // Fonction de confirmation de l'hypothèse
    function confirmSelection() {
    let selectedPersonnage = document.querySelector('.personnages .highlighted');
    let selectedSalle = document.querySelector('.salle .highlighted');
    let selectedArme = document.querySelector('.armes .highlighted');

    if (selectedPersonnage && selectedSalle && selectedArme) {
        let hypothesePersonnage = selectedPersonnage.innerText;
        let hypotheseSalle = selectedSalle.innerText;
        let hypotheseArme = selectedArme.innerText;

        let personnage = '<?php echo $_SESSION['personnage_tire']; ?>';
        let salle = '<?php echo $_SESSION['piece']; ?>';
        let arme = '<?php echo $_SESSION['arme_tiree']; ?>';

        let incorrectElements = [];
        if (hypothesePersonnage !== personnage) incorrectElements.push("Personnage : " + hypothesePersonnage);
        if (hypotheseSalle !== salle) incorrectElements.push("Salle : " + hypotheseSalle);
        if (hypotheseArme !== arme) incorrectElements.push("Arme : " + hypotheseArme);

        if (incorrectElements.length > 0) {
            
            let incorrectElement = incorrectElements[Math.floor(Math.random() * incorrectElements.length)];

            fetch('Sauvegarde/Element-Incorect.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'incorrectElement=' + encodeURIComponent(incorrectElement)
            })
            // Message apres confirmation si l'hypothese est fause
            .then(response => response.text())
            .then(data => {
                document.body.innerHTML = `
                    <div class="result">
                        <div id="personnage-message">
                            <h5><u>L'hypothèse est incorrecte</u></h5>
                            <br>
                            <p>Personnage : ${hypothesePersonnage}</p>
                            <p>Salle : ${hypotheseSalle}</p>
                            <p>Arme : ${hypotheseArme}</p>
                            <br>
                            <p>Élément incorrect : <strong>${incorrectElement}</strong></p>
                            <br>
                            <button id="suivant" onclick="window.location.href='Salle-Choix.php';">Suivant</button>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi des données :', error);
            });
        // Si l'hypothese est vrai alors sa revoie vers la fin
        } else {
            window.location.href = 'Fin.php';
        }
    } else {
        alert("Veuillez sélectionner un personnage, une salle et une arme.");
    }

    return false; 
}
</script>
</head>
<body onload="showInfoPanel()">

<!-- Plan du Manoir -->
<img src="img\autre\Plan_Manoir.png" alt="Afficher l'image" class="image-thumbnail" id="showButton4">

<div class="overlay4" id="overlay4"></div>

<div class="image-modal4" id="imageModal4">
    
    <button class="button4" id="closeButton4">Fermer l'image</button>
    <img src="img\autre\Plan_Manoir.png" alt="Image Exemple">
</div>

<script>

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

<header id="titre-header">
    <h1 class="titre"><?php echo htmlspecialchars($pieceActuelle); ?></h1>
    <div class="choix-container">
        <h2 class="sous-titre">Faites une hypothèse</h2>
    </div>
</header>

<div class="content-container">

<!-- Personnages -->
<div class="personnages-container">
    <h3>Personnages :</h3>
    <div class="button-container personnages">
        <?php
        $sql = 'SELECT id_personnage, nom_personnage FROM personnages';
        $liste_personnages = $sqlite->query($sql);

        if ($liste_personnages) {
            while ($row = $liste_personnages->fetchArray(SQLITE3_ASSOC)) {
                $id_personnage = htmlspecialchars($row['id_personnage']);
                $nom_personnage = htmlspecialchars($row['nom_personnage']);

                // Ne pas afficher le personnage sélectionné
                if ($nom_personnage == $nom_personnage_bdd) {
                    continue;
                }

                // Afficher l'image et le bouton
                echo '<form method="POST" style="display: inline-block; text-align: center;">';
                echo '<img src="img/personnages/' . $id_personnage . '.webp" alt="' . $nom_personnage . '" class="personnage-image">';
                echo '<button type="button" onclick="toggleHighlight(this, \'personnages\')" name="personnage">' . $nom_personnage . '</button>';
                echo '</form>';
            }
        } else {
            echo "Aucun personnage disponible.";
        }
        ?>
    </div>
</div>

<!-- Salle -->
<div class="salle-container">
    <h3>Salle :</h3>
    <div class="button-container salle">
        <?php
        // Récupérer l'ID et le nom de la salle actuelle
        $sql = 'SELECT id_piece FROM pieces WHERE nom_piece = :piece';
        $requete = $sqlite->prepare($sql);
        $requete->bindValue(':piece', $pieceActuelle, SQLITE3_TEXT);
        $result = $requete->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row) {
            $id_piece = htmlspecialchars($row['id_piece']);
            echo '<form method="POST" style="display: inline-block; text-align: center;">';
            echo '<img src="img/PieceDuManoir/' . $id_piece . '.webp" alt="' . $pieceActuelle . '" class="salle-image">';
            echo '<button type="button" onclick="toggleHighlight(this, \'salle\')" name="salle">' . $pieceActuelle . '</button>';
            echo '</form>';
        } else {
            echo "Aucune salle disponible.";
        }
        ?>
    </div>
</div>

<!-- Armes -->
<div class="armes-container">
    <h3>Armes :</h3>
    <div class="button-container armes">
        <?php
        $sql = 'SELECT id_arme, nom_arme FROM armes';
        $liste_armes = $sqlite->query($sql);

        if ($liste_armes) {
            while ($row = $liste_armes->fetchArray(SQLITE3_ASSOC)) {
                $id_arme = htmlspecialchars($row['id_arme']);
                $nom_arme = htmlspecialchars($row['nom_arme']);

                echo '<form method="POST" style="display: inline-block; text-align: center;">';
                echo '<img src="img/armes/' . $id_arme . '.webp" alt="' . $nom_arme . '" class="arme-image">';
                echo '<button type="button" onclick="toggleHighlight(this, \'armes\')" name="arme">' . $nom_arme . '</button>';
                echo '</form>';
            }
        } else {
            echo "Aucune arme disponible.";
        }
        ?>
    </div>
</div>

    <div id="confirmation-message" style="text-align: center; font-weight: bold;"><?php echo $confirmation_message; ?></div>
    
    <div class="confirmer-container">
        <form method="POST" onsubmit="return confirmSelection()">
            <button type="submit" name="confirmer" class="confirmer-button">Confirmer l'hypothèse</button>
        </form>
    </div>

</body>

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

    <!-- Bloc Note -->
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

</html>

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

<?php 
  $sqlite->close(); // Fermeture de la connexion à la base de données SQLite
?>