<?php
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class FormulaireAppelOffre {
    private $dompdf;
    private $donnees;

    public function __construct() {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $this->dompdf = new Dompdf($options);
    }

    public function setDonnees($donnees) {
        $this->donnees = $donnees;
    }

    public function genererHTML() {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    margin: 20px;
                    line-height: 1.4;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #0066cc;
                    padding-bottom: 10px;
                }
                .logo-section {
                    background-color: #0066cc;
                    color: white;
                    padding: 10px 15px;
                    font-weight: bold;
                    font-size: 14px;
                }
                .title-section {
                    flex-grow: 1;
                    text-align: center;
                    font-weight: bold;
                    font-size: 16px;
                    text-transform: uppercase;
                }
                .form-section {
                    margin: 20px 0;
                }
                .checkbox-group {
                    margin: 10px 0;
                }
                .checkbox {
                    display: inline-block;
                    width: 15px;
                    height: 15px;
                    border: 2px solid #333;
                    margin-right: 10px;
                    text-align: center;
                    line-height: 11px;
                    font-weight: bold;
                }
                .checkbox.checked {
                    background-color: #333;
                    color: white;
                }
                .field-group {
                    margin: 15px 0;
                }
                .field-label {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .field-value {
                    border-bottom: 1px solid #333;
                    min-height: 20px;
                    padding: 2px 0;
                    display: inline-block;
                    width: 300px;
                }
                .instructions {
                    font-size: 10px;
                    color: #666;
                    margin-top: 20px;
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                td {
                    padding: 5px;
                    vertical-align: top;
                }
                .section-title {
                    font-weight: bold;
                    text-decoration: underline;
                    margin: 20px 0 10px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logo-section">
                    ' . ($this->donnees['numero_organisme'] ?? '05') . '
                </div>
                <div class="title-section">
                    AVIS DE FIRST AID APPEL
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">TYPE DE PROCÉDURE :</div>
                
                <div class="checkbox-group">
                    <span class="checkbox ' . ($this->donnees['type_procedure'] == 'appel_offres_ouvert' ? 'checked' : '') . '">
                        ' . ($this->donnees['type_procedure'] == 'appel_offres_ouvert' ? '✓' : '') . '
                    </span>
                    APPEL D\'OFFRES OUVERT
                </div>
                
                <div class="checkbox-group">
                    <span class="checkbox ' . ($this->donnees['type_procedure'] == 'appel_offres_restreint' ? 'checked' : '') . '">
                        ' . ($this->donnees['type_procedure'] == 'appel_offres_restreint' ? '✓' : '') . '
                    </span>
                    APPEL D\'OFFRES RESTREINT
                </div>
                
                <div class="checkbox-group">
                    <span class="checkbox ' . ($this->donnees['type_procedure'] == 'concours' ? 'checked' : '') . '">
                        ' . ($this->donnees['type_procedure'] == 'concours' ? '✓' : '') . '
                    </span>
                    CONCOURS
                </div>
                
                <div class="checkbox-group">
                    <span class="checkbox ' . ($this->donnees['type_procedure'] == 'procedure_negociee' ? 'checked' : '') . '">
                        ' . ($this->donnees['type_procedure'] == 'procedure_negociee' ? '✓' : '') . '
                    </span>
                    PROCÉDURE NÉGOCIÉE
                </div>
            </div>

            <table>
                <tr>
                    <td style="width: 30%;">
                        <div class="field-label">Organisme :</div>
                        <div class="field-value">' . htmlspecialchars($this->donnees['organisme'] ?? '') . '</div>
                    </td>
                    <td>
                        <div class="field-label">Adresse :</div>
                        <div class="field-value">' . htmlspecialchars($this->donnees['adresse'] ?? '') . '</div>
                    </td>
                </tr>
            </table>

            <div class="field-group">
                <div class="field-label">Code postal/Localité :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['code_postal'] ?? '') . ' / ' . htmlspecialchars($this->donnees['localite'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Téléphone :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['telephone'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Télécopieur :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['telecopieur'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Adresse électronique :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['email'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Objet du marché :</div>
                <div class="field-value" style="width: 500px;">' . htmlspecialchars($this->donnees['objet_marche'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Lieu d\'exécution :</div>
                <div class="field-value" style="width: 400px;">' . htmlspecialchars($this->donnees['lieu_execution'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Date limite de remise des offres :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['date_limite'] ?? '') . '</div>
            </div>

            <div class="field-group">
                <div class="field-label">Heure limite :</div>
                <div class="field-value">' . htmlspecialchars($this->donnees['heure_limite'] ?? '') . '</div>
            </div>

            <div class="instructions">
                <p><strong>Instructions :</strong></p>
                <p>Les entreprises intéressées sont priées de se conformer aux conditions du cahier des charges et de remettre leurs offres dans les délais impartis.</p>
                <p>Ce document doit être complété avec toutes les informations requises et transmis selon les modalités prévues.</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    public function genererPDF($nomFichier = 'formulaire_appel_offre.pdf') {
        $html = $this->genererHTML();

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        // Sauvegarder le fichier
        file_put_contents($nomFichier, $this->dompdf->output());

        return $nomFichier;
    }

    public function afficherPDF() {
        $html = $this->genererHTML();

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        // Afficher directement dans le navigateur
        $this->dompdf->stream('formulaire_appel_offre.pdf', array('Attachment' => false));
    }
}

// Exemple d'utilisation
try {
    // Données à remplir dans le formulaire
    $donnees = [
        'numero_organisme' => '05',
        'type_procedure' => 'appel_offres_ouvert', // Options: appel_offres_ouvert, appel_offres_restreint, concours, procedure_negociee
        'organisme' => 'Ministère des Travaux Publics',
        'adresse' => '123 Avenue de la République',
        'code_postal' => '12345',
        'localite' => 'Ville Exemple',
        'telephone' => '+33 1 23 45 67 89',
        'telecopieur' => '+33 1 23 45 67 90',
        'email' => 'contact@ministere-tp.gov',
        'objet_marche' => 'Construction d\'infrastructure routière - Route nationale N°15',
        'lieu_execution' => 'Région Sud, Département 05',
        'date_limite' => '15 septembre 2025',
        'heure_limite' => '16h00'
    ];

    // Créer une instance du générateur de formulaire
    $formulaire = new FormulaireAppelOffre();
    $formulaire->setDonnees($donnees);

    // Option 1: Sauvegarder le PDF
    $fichierGenere = $formulaire->genererPDF('appel_offre_' . date('Y-m-d') . '.pdf');
    echo "PDF généré avec succès : " . $fichierGenere . "\n";

    // Option 2: Afficher directement dans le navigateur (décommentez si nécessaire)
    // $formulaire->afficherPDF();

} catch (Exception $e) {
    echo "Erreur lors de la génération du PDF : " . $e->getMessage() . "\n";
}

// Fonction utilitaire pour générer rapidement un formulaire
function genererFormulaireRapide($donnees, $nomFichier = null) {
    $formulaire = new FormulaireAppelOffre();
    $formulaire->setDonnees($donnees);

    if ($nomFichier) {
        return $formulaire->genererPDF($nomFichier);

    } else {
        $formulaire->afficherPDF();
    }
}

?>