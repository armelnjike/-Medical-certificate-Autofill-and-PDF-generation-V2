<?php
require_once '../server/database.php';

header('Content-Type: application/json');
// Vérifier la connexion
$con = getDbConnection();
if ($con->connect_error) {
    die("Erreur connexion: " . $con->connect_error);
}

// Récupérer l'ID
$id = intval($_GET['id'] ?? 0);

// Vérifier dans la table "documents"
$sql = "SELECT * FROM doc_verification WHERE id_doc= $id LIMIT 1";
$result = $con->query($sql);

if ($result && $result->num_rows > 0) {
    $doc = $result->fetch_assoc();
    echo "<h1 style='color:green;'>✅ Document valide</h1>";
    echo "<p>Nom : {$doc['owner']}</p>";
    echo "<p>Date : {$doc['creation_date']}</p>";
} else {
    echo "<h1 style='color:red;'>❌ Document invalide</h1>";
}

$con->close();
