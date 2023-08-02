<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
if(isset($_GET["id_conc"])) {
    $id_conc = $_GET["id_conc"];
    // Vérification que l'id est bien un nombre et est positif
    if (is_numeric($id_conc) && $id_conc >= 0) {

        // Vérification que le participant est connecté
        if (isset($_SESSION['participant'])) {
            try {
                // Vérification que le concours est ouvert aux participations (par la date de début et de fin de ce dernier)
                $reqConc = $bdd->prepare("SELECT start_date, end_date FROM giveaway WHERE giveaway.id_conc = :id_conc");
                $reqConc->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
                $reqConc->execute();
                $dataConc = $reqConc->fetch();

                $date = date("Y-m-d H:i:s");
                // Si le concours est en cours
                if ($dataConc['start_date'] <= $date && $dataConc['end_date'] > $date) {
                    // Vérification que le participant ne participe pas déjà
                    $check = $bdd->prepare("SELECT id_part password FROM participate WHERE id_part = :id_part AND id_conc = :id_conc");
                    $check->bindValue(':id_part', $_SESSION['participant'][0], PDO::PARAM_INT);
                    $check->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
                    $check->execute();
                    $data = $check->fetch();
                    $row = $check->rowCount();

                    if ($row == 0) {
                        // Récupérer le participant dans la base de données à partir de la session
                        $insert = $bdd->prepare('INSERT INTO participate(id_part, id_conc) VALUES(:id_part,:id_conc)');
                        $insert->bindValue(':id_part', $_SESSION['participant'][0], PDO::PARAM_INT);
                        $insert->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
                        $insert->execute();
                        echo 'You are participating to this giveaway!';
                    }
                    else {
                        die('<div class="error">You are already participating to this giveaway! Go to <a href="index.php">Home</a></div>');
                    }
                }
                // Sinon si le concours est fini
                else if ($dataConc['end_date'] < $date) {
                    die('<div class="Error">This giveaway has ended, you can\'t participate! Go to <a href="index.php">Home</a></div>');
                }
                // Sinon le concours n'a pas commencé
                else {
                    die('<div class="Error">This giveaway has not started yet, you can\'t participate! Go to <a href="index.php">Home</a></div>');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e;
            }
        }
        else {
            die('<div class="error">You must be logged in as a participant to participate! <a href="login.php">Login</a></div>');
        }
    }
    else {
        die('<div class="Error">Giveaway\'s id is not valid! Go to <a href="index.php">Home</a></div>');
    }
}
else {
    die('<div class="Error">Giveaway\'s id is missing! Go to <a href="index.php">Home</a></div>');
}
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>