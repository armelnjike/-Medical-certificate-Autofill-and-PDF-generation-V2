<?php
// Fichier test-pdf.php - Test isolé pour générer un PDF

// Démarrer la capture de sortie
ob_start();

require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

try {
    // HTML simple pour test
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Test PDF</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .header { text-align: center; color: #333; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>TEST PDF SIMPLE</h1>
            <p>Si vous voyez ce PDF, la génération fonctionne !</p>
        </div>
        
        <h2>Données de test :</h2>
        <ul>
            <li>Patient: Jean Dupont</li>
            <li>Date: 2025-01-15</li>
            <li>Téléphone: 01 23 45 67 89</li>
        </ul>
        
        <p style="margin-top: 50px; text-align: center;">
            <strong>Document généré le ' . date('Y-m-d H:i:s') . '</strong>
        </p>
    </body>
    </html>';

    // Nettoyer les buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Configurer DOMPDF
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', false);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfContent = $dompdf->output();

    if (empty($pdfContent)) {
        throw new Exception('PDF vide généré');
    }

    // Headers pour PDF
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfContent));
    header('Content-Disposition: inline; filename="test.pdf"');
    header('Cache-Control: no-cache');

    echo $pdfContent;
    exit();

} catch (Exception $e) {
    // Nettoyer les buffers en cas d'erreur
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Afficher l'erreur
    header('Content-Type: text/html; charset=utf-8');
    echo "<h1>Erreur de test PDF</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>