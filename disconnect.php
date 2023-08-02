<?php
    // Système de déconnexion
    session_start();
    session_destroy();
    header('Location:index.php');
?>