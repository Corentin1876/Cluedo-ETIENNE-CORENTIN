<?php
session_start();

// Vérifier si la donnée a été envoyée
if (isset($_POST['incorrectElement'])) {
    // Stocker l'élément incorrect dans la session
    $_SESSION['incorrectElement'] = $_POST['incorrectElement'];
} else {
    // Si aucune donnée n'a été envoyée, rediriger ou afficher un message d'erreur
    echo "Erreur : Élément incorrect non reçu.";
}
?>