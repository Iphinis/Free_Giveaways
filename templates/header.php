<?php
session_start();
date_default_timezone_set('Europe/Amsterdam');
?>
<html lang="en">
    <head>
        <!-- Pour éviter des erreurs liées aux caractères spéciaux nous définissons l'encodage à l'UTF-8 -->
        <meta charset="UTF-8">
        <!-- Pour contrôler la mise en page sur les navigateurs mobiles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Nous définissons un titre -->
        <title>Free Giveaways</title>
        <!-- Nous définissons une icône -->
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />

        <!-- Lien du css à la page html -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <!-- Lien du css pour les particules à la page html -->
        <link rel="stylesheet" type="text/css" href="css/particles.css">
        
        <!-- Récupération de polices Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Raleway&family=Roboto:wght@500;700&display=swap" rel="stylesheet">
        <!-- Récupération d'icônes sur fontawesome -->
        <script src="https://kit.fontawesome.com/60e154744a.js" crossorigin="anonymous"></script>
        <!-- Récupération du Jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
        <header>    
            <nav>
                <div class="hamburger">
                    <i class="fa fa-bars fa-2x"></i>
                </div>
                <div class="logo">
                    <a href="index.php"><img alt="Free Giveaways" src="images/logo.png"></a>
                </div>
                <div class="menu">
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <?php if(isset($_SESSION['organizer'])) echo '<li><a href="creategiveaway.php"><i class="fas fa-plus-square"></i> Create a Giveaway</a></li>'; ?>
                        <li><a href="searchgiveaway.php"><i class="fas fa-search"></i> Search a Giveaway</a></li>
                        <li><?php
                        if (isset($_SESSION['organizer'])) {
                            echo '<a href="organizer.php?id_org=' . $_SESSION['organizer'][0] . '"><i class="fas fa-user"></i> ' . $_SESSION['organizer'][1] . '</a>';
                        }
                        else if (isset($_SESSION['participant'])) {
                            echo '<a href="participant.php?id_part=' . $_SESSION['participant'][0] . '"><i class="fas fa-user"></i> ' . $_SESSION['participant'][1] . '</a>';
                        }
                        else {
                            echo '<a href="login.php"><i class="fas fa-user"></i> Login</a>';
                        }
                        ?></li>
                        <?php if (isset($_SESSION['organizer']) || isset($_SESSION['participant'])) echo '<li><a href="disconnect.php" title="Disconnect"><i class="fas fa-power-off"></i></a></li>'; ?>
                    </ul>
                </div>
            </nav>
        </header>

        <div class="particles" id="particle1"></div>
        <div class="particles" id="particle2"></div>
        <div class="particles" id="particle3"></div>
        <div class="particles" id="particle4"></div>
        <div class="particles" id="particle5"></div>

        <div class="content">