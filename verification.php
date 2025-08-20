<?php
require_once 'server/database.php';

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

// Définir les infos
$valid = false;
$doc = null;
if ($result && $result->num_rows > 0) {
    $doc = $result->fetch_assoc();
    $valid = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .valid {
            border-left: 6px solid #28a745;
        }
        .invalid {
            border-left: 6px solid #dc3545;
        }
        .container {
            margin-top: 80px;
        }
        h1 {
            font-size: 2rem;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card p-4 <?php echo $valid ? 'valid' : 'invalid'; ?>" style="max-width: 500px;">
        <?php if ($valid): ?>
            <h1 class="text-success text-center">✅ Document valide</h1>
            <p><strong>Nom du propriétaire :</strong> <?= htmlspecialchars($doc['owner']) ?></p>
            <p><strong>Date de création :</strong> <?= htmlspecialchars($doc['creation_date']) ?></p>
        <?php else: ?>
            <h1 class="text-danger text-center">❌ Document invalide</h1>
            <p class="text-center">Aucun document trouvé avec cet identifiant.</p>
        <?php endif; ?>
    </div>
</div>
<?php $con->close(); ?>
</body>
</html>

