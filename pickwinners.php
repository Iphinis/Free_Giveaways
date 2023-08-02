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

        // Vérification que l'organisateur est connecté
        if (isset($_SESSION['organizer'])) {
            try {
                // Récupérer l'organisateur dans la base de données à partir de l'email
                $check = $bdd->prepare("SELECT giveaway.title, giveaway.winners FROM organizer JOIN giveaway ON organizer.id_org = giveaway.id_org WHERE organizer.id_org = :id_org AND giveaway.id_conc = :id_conc");
                $check->bindValue(':id_org', $_SESSION['organizer'][0], PDO::PARAM_INT);
                $check->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
                $check->execute();
                $data = $check->fetch();
                $row = $check->rowCount();

                // Si l'organisateur est le même que celui qui a créé le concours (trouvé à partir de l'id)
                if ($row == 1) {
                    // Nombre de gagnants
                    $winners_amount = $data['winners'];
                    // Nom du concours
                    $giveaway_title = $data['title'];

                    // Tirage au sort
                    $check = $bdd->prepare("SELECT DISTINCT name, email, address, phone_number FROM participate JOIN giveaway ON participate.id_conc = giveaway.id_conc JOIN participant ON participate.id_part = participant.id_part WHERE participate.id_conc = :id_conc ORDER BY RAND() LIMIT :winners");
                    $check->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
                    $check->bindValue(':winners', $winners_amount, PDO::PARAM_INT);
                    $check->execute();
                    $data = $check->fetchAll();
                    $row = $check->rowCount();

                    // Afficher les informations des gagnants
                    if ($row > 0) {
                        echo 'Giveaway: ' . $giveaway_title . '
                        <br>Winner(s): ' . $winners_amount;
                        foreach ($data as $winner => $info) {
                            echo "<table class=\"giveaway\">
                            <thead>
                            <tr>
                                <th>Winner</th>
                                <th>#" . intval($winner + 1) . "</th>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <th>{$info["name"]}</th>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <th>{$info["email"]}</th>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <th>{$info["address"]}</th>
                            </tr>
                            <tr>
                                <th>Phone number</th>
                                <th>{$info["phone_number"]}</th>
                            </tr>
                            </thead>
                            </table>";

                        }
                    }
                    else {
                        die('<div class="error">No participant were found for this giveaway!</div>');
                    }
                }
                else {
                    die('<div class="error">You aren\'t the organizer of this giveaway!</div>');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e;
            }
        }
        else {
            die('<div class="error">You must be logged in as an organizer to pick winners! <a href="login.php">Login</a></div>');
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