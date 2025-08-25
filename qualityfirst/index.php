<?php
session_start();

$inactivityLimit = 30*60*1000;

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
// Pas connecté => rediriger vers la page de login
header('Location: ../administration/login.php');
exit();
}

// Vérifie la durée d'inactivité
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactivityLimit) {
// Session expirée
session_unset();
session_destroy();
header("Location: https://foyetmedical.fagiciel.com/administration/login.php");
exit();
}
// Mise à jour de l'heure de dernière activité
$_SESSION['last_activity'] = time();

// Détermine la page actuelle
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSI American Safety Health Institute</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Navbar Styles - Version Corrigée */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            text-decoration: none;
        }

        .navbar-brand svg {
            width: 32px;
            height: 32px;
            margin-right: 10px;
            fill: #4facfe;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            /* Supprimer margin et flex-grow pour permettre justify-content de fonctionner */
            margin: 0;
            width: 100%;
            justify-content: space-between;
        }

        /* Conteneur pour les liens principaux */
        .nav-main-links {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Conteneur pour le logout */
        .nav-logout-container {
            margin-left: auto;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: #2d3748;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: transparent;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            fill: currentColor;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .nav-link.logout {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .nav-link.logout:hover {
            background: linear-gradient(135deg, #ff5252 0%, #e53e3e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #2d3748;
            cursor: pointer;
            padding: 0.5rem;
        }

        .nav-toggle:hover {
            color: #4facfe;
        }

        /* Mobile Navbar */
        @media (max-width: 768px) {
            .nav-toggle {
                display: block;
            }

            .navbar-nav {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                border-top: 1px solid rgba(255, 255, 255, 0.2);
                padding: 1rem;
                flex-direction: column;
                gap: 0.5rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                display: none;
                justify-content: flex-start;
            }

            .navbar-nav.active {
                display: flex;
            }

            .nav-main-links {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }

            .nav-logout-container {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
            }

            .nav-link {
                width: 100%;
                justify-content: center;
                padding: 1rem;
            }

            .navbar-container {
                position: relative;
            }
        }
        /* Main Content */
        .main-content {
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(255,255,255,0.05) 10px,
                    rgba(255,255,255,0.05) 20px
            );
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .medical-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .medical-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .form-section {
            padding: 0;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 600;
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #4facfe, #00f2fe, #4facfe);
        }

        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            fill: currentColor;
        }

        .section-content {
            padding: 40px;
            background: #fafbfc;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
            position: relative;
        }
        .checkbox{
            display: flex;
            flex-wrap: wrap;   /* retour à la ligne si trop long */
            gap: 16px;
            align-content: center;
            margin-left: auto;
            margin-right: auto;

        }
        .checkbox label{
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox input{
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgb(189,231,232);
            width: 20px;
            height: 20px;
        }
        .checkbox input:checked + span{}

        .required::after {
            content: ' *';
            color: #e53e3e;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            transform: translateY(-1px);
        }

        input:hover, select:hover, textarea:hover {
            border-color: #cbd5e0;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%23666' d='M2 0L0 2h4z'/><path fill='%23666' d='M0 3l2 2 2-2z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
        }

        .medical-tests {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .medical-tests h3 {
            color: #2d3748;
            font-size: 1.4rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .test-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            fill: #4a5568;
        }

        .ppd-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .submit-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
        }

        .submit-btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 18px 50px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 172, 254, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .changeTemplate-btn {
            background: linear-gradient(135deg, #000a0e 0%, #0f00fa0f 100%);
            color: white;
            border: none;
            padding: 18px 50px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .changeTemplate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(100, 152, 254, 0.4);
        }

        .changeTemplate-btn:active {
            transform: translateY(1);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .section-content {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .ppd-grid {
                grid-template-columns: 1fr;
            }
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-circle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: -2s;
        }

        .floating-circle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: -4s;
        }

        .floating-circle:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: -1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
<!-- Navigation Bar -->
<!-- Navigation Bar - Structure Corrigée -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="#" class="navbar-brand">
            <svg viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            HSI Medical
        </a>

        <button class="nav-toggle" onclick="toggleNavbar()">
            ☰
        </button>

        <ul class="navbar-nav" id="navbarNav">
            <!-- Liens principaux -->
            <div class="nav-main-links">
                <li class="nav-item">
                    <a href="../index.php" class="nav-link <?php echo ($current_page == 'page1') ? 'active' : ''; ?>">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Foyet-medical
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../hsi/index.php" class="nav-link">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        Hsi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active " >
                        <svg viewBox="0 0 24 24">
                            <path d="M14,17H7V15H14M17,13H7V11H17M17,9H7V7H17M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C0,3.89 20.1,3 19,3Z"/>
                        </svg>
                        Quality First
                    </a>
                </li>
                <li class="nav-item" style="display: none !important;">
                    <a href="#" class="nav-link">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        Editor
                    </a>
                </li>
            </div>

            <!-- Bouton Logout à droite -->
            <div class="nav-logout-container">
                <li class="nav-item">
                    <a href="../server/logout.php" class="nav-link logout" onclick="return confirm('Are you sure you want to logout?')">
                        <svg viewBox="0 0 24 24">
                            <path d="M17,17.25V14H10V10H17V6.75L22.25,12L17,17.25M13,2A2,2 0 0,1 15,4V8H13V4H4V20H13V16H15V20A2,2 0 0,1 13,22H4A2,2 0 0,1 2,20V4A2,2 0 0,1 4,2H13Z"/>
                        </svg>
                        Logout
                    </a>
                </li>
            </div>
        </ul>
    </div>
</nav>

<div class="floating-elements">
    <div class="floating-circle"></div>
    <div class="floating-circle"></div>
    <div class="floating-circle"></div>
</div>

<div class="main-content">
    <div class="container">

        <div class="header">
            <div class="header-content">
                <div class="medical-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/>
                    </svg>
                </div>
                <h1>HSI Registration</h1>
                <p>ADULT FIRST AID | CPR AED</p>
            </div>

        </div>

        <form id="patientForm" action="#" method="POST">


            <!-- Student Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Student Informations
                </div>
                <div class="section-content">
                    <div class="medical-tests">

                        <div class="ppd-grid">
                            <div class="form-group">
                                <label for="studentName">student Name</label>
                                <input type="text" id="studentName" name="studentName" placeholder="studentName">
                            </div>
                            <div class="form-group">
                                <label for="className" class="required">class </label>
                                <input type="text" id="className" name="className" required placeholder="Enter instructor's full name">
                            </div>

                            <div class="form-group">
                                <label for="authorizingStudent" class="required">authorizing Studentr Name</label>
                                <input type="text" id="authorizingStudent" name="authorizingStudent" required placeholder="authorizing Student Name">
                            </div>
                            <div class="form-group">
                                <label for="studentSignature" class="required">student Signing Name</label>
                                <input type="text" id="studentSignature" name="studentSignature" required placeholder="authorizing Student Name">
                            </div>
                            <div class="form-group">
                                <label for="studentSignatureDate" class="required">authorizing Studentr Name</label>
                                <input type="date" id="studentSignatureDate" name="studentSignatureDate" required placeholder="student Signature Date">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <!-- Patient Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Required to begin program
                </div>
                <div class="section-content" style = "margin: 5px; !important;">

                    <div class="form-grid">
                        PPD or Chest X-ray


                        <div class="form-group">
                            <label for="tbTestDate">Date</label>
                            <input type="date" id="tbTestDate" name="tbTestDate" placeholder="Date">

                            <label for="tbResult" class="required">Result</label>
                            <input type="text" id="tbResult" name="tbResult" required placeholder="tb Result">
                        </div>
                    </div>
                </div>
                <div class="section-content" style = "margin: 5px; !important;">
                    <div class="form-grid">
                        <div class="form-group checkbox" >
                            <p>Student is <strong>FREE</strong> from TB or other communicable disease which might present a health hazard to patients or other personnel.<br>Yes / No with checkboxes</p>
                            <label for="checkbox1">
                                <input id="checkbox1" type="radio" name="checkbox1" value="yes" required> Yes
                            </label>
                            <label for="checkbox2">
                                <input id="checkbox2" type="radio" name="checkbox1" value="no"> No
                            </label>


                        </div>
                        <div class="form-group">
                            <label for="tbExplaination">If no please explain</label>
                            <input type="text" id="tbExplaination" name="tbExplanation" placeholder="tbExplaination">
                        </div>

                    </div>
                </div>



                <div class="section-content">
                    <div class="form-grid">
                        <div class="form-group checkbox" >
                            <p class="mb-2 small">The above named student is in satisfactory physical condition.<br>Yes / No with checkboxes</p> <br>
                            <label for="checkbox3">
                                <input id="checkbox3" type="radio" name="checkbox3" value="yes" required> Yes
                            </label>
                            <label for="checkbox4">
                                <input id="checkbox4" type="radio" name="checkbox3" value="no" > No
                            </label>
                        </div>



                                <div class="form-group">
                                    <label for="phExplaination">If no please explain</label>
                                    <input style="width: 100%;" type="text" id="phExplaination" name="phExplanation" placeholder="phisic Explaination">
                                </div>

                    </div>

                </div>
            </div>

            <!-- Practical care examinator Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Primary Care Provider
                </div>
                <div class="section-content">
                    <div class="medical-tests">

                        <div class="ppd-grid">
                            <div class="form-group">
                                <label for="PrimaryCareProvider">Primary Care Provider</label>
                                <input type="text" id="PrimaryCareProvider" name="PrimaryCareProvider" placeholder="Primary Care Provider">
                            </div>
                            <div class="form-group">
                                <label for="formCompletedDate" class="required">form Completed Date</label>
                                <input type="date" id="formCompletedDate" name="formCompletedDate" required placeholder="form Completed Date">
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <!-- Facility Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Primary Care Provider
                </div>
                <div class="section-content">
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="PrimaryCarePSignDate" class="required">Primary CareP Signed Date</label>
                            <input type="date" id="PrimaryCarePSignDate" name="PrimaryCarePSignDate" required placeholder="Primary CareP Signed Date">
                        </div>
                        <div class="form-group">
                            <label for="PrimaryCarePAddress" class="required">Training Center Id</label>
                            <input type="text" id="PrimaryCarePAddress" name="PrimaryCarePAddress" required placeholder="Primary CareP Address">
                        </div>
                    </div>
                </div>
            </div>


            <!-- Submit Section -->
            <div class="submit-section">
                <button type="submit" class="submit-btn">Print Patient Registration</button>
                <p style="color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 0.9rem;">

                </p>

                <p style="color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 0.9rem;">
                    All information will be securely processed and stored according to HIPAA regulations
                </p>
            </div>
        </form>
    </div>

    <script>
        function toggleNavbar() {
            const nav = document.getElementById("navbarNav");
            nav.classList.toggle("active");
        }


        document.getElementById('patientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic form validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e53e3e';
                    isValid = false;
                } else {
                    field.style.borderColor = '#4facfe';
                }
            });

            if (isValid) {
                // Show success message
                const submitBtn = document.querySelector('.submit-btn');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Registration Submitted Successfully ✓';
                submitBtn.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';

                // Collect form data

                const formData = new FormData(this);
                console.log(formData);

                fetch('defaulttemplate.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok.');
                        }
                        return response.blob(); // or response.json() if expecting JSON
                    })
                    .then(blob => {
                        const blobUrl = URL.createObjectURL(blob);

                        // Open in a new tab
                        const pdfWindow = window.open(blobUrl);

                        // Optional: Trigger print automatically after PDF loads
                        pdfWindow.onload = () => {
                            pdfWindow.print();
                        };

                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                        alert('Une erreur est survenue lors de la génération du PDF.');
                    });

                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.style.background = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
                    // Here you would normally submit the form data
                    console.log('Form would be submitted with:', new FormData(this));
                }, 2000);
            } else {
                alert('Please fill in all required fields.');
            }
        });

        // Add focus animations
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });


        (function () {
            let timeout;

            function resetTimer() {
                clearTimeout(timeout);
                // Redémarre le compte à rebours de 2 min
                timeout = setTimeout(logout, 10 * 60 * 1000); // 120000 ms = 2 minutes
            }

            function logout() {
                window.location.href = "administration/login.php";
            }

            // Écoute tous les types d'activité
            ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetTimer);
            });

            // Initialiser au chargement
            resetTimer();
        })();

    </script>
</body>
</html>