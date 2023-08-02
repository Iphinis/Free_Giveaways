<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
    if(isset($_GET["id_part"])) {
        $id_part = htmlspecialchars($_GET["id_part"]);
        // Vérification que l'id est bien un nombre et est positif
        if (is_numeric($id_part) && $id_part >= 0) {
            try {
                // Requête qui contient toutes les données nécessaires par la suite
                $req = $bdd->prepare("SELECT id_part, name, email, address, phone_number FROM participant WHERE id_part = :id_part");
                $req->bindValue(':id_part', $id_part, PDO::PARAM_INT);
                $req->execute();
                $dataPart = $req->fetch();
                $row = $req->rowCount();

                // Si l'organisateur existe
                if ($row == 1) {
                    // Si connecté en tant qu'organisateur et même compte que le compte demandé alors afficher les informations privées si le compte organisateur est le même que celui de la session
                    if (isset($_SESSION['participant']) && $_SESSION['participant'][0] == $id_part) {
                        echo "<table class=\"giveaway\">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>{$dataPart["name"]}</th>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th>{$dataPart["email"]}</th>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <th>{$dataPart["address"]}</th>
                        </tr>
                        <tr>
                            <th>Phone number</th>
                            <th>{$dataPart["phone_number"]}</th>
                        </tr>
                        </thead>
                        </table>";
                        // Récupérer tous les concours auquel le participant a participé
                        $reqConc = $bdd->prepare("SELECT giveaway.id_conc, title, description, end_date FROM giveaway JOIN participate ON giveaway.id_conc = participate.id_conc JOIN participant ON participate.id_part = participant.id_part WHERE participant.id_part = :id_part AND start_date <= NOW()");
                        $reqConc->bindValue(':id_part', $id_part, PDO::PARAM_INT);
                        $reqConc->execute();
                        $result = $reqConc->fetchAll(PDO::FETCH_ASSOC);

                        if ($result != NULL) {
                            echo '<h1>Your participations:</h1>';
                            // Afficher tous les concours auquel le participant a participé
                            foreach($result as $key => $row) {
                                strlen($row['description']) > 100 ? $small_description = substr($row['description'], 0, 100) . " [...]" : $small_description = $row['description'];
                                print_r("<div class=\"result\">
                                    <a href=\"giveaway.php?id_conc={$row['id_conc']}\">{$row['title']}</a><br>
                                    {$small_description}<br>
                                    End at " . date("d/m/Y H:i:s", strtotime($row['end_date'])) . "</div>");
                            }
                        }
                    }
                    // Sinon afficher une erreur
                    else {
                        die('<div class="Error">You can\'t see this page! Go to <a href="index.php">Home</a></div>');
                    }
                }
                else {
                    die('<div class="Error">Organizer doesn\'t exist! Go to <a href="index.php">Home</a></div>');
                }

            } catch (PDOException $e) {
                echo 'Error: ' . $e;
            }
        }
        else {
            die('<div class="Error">Participant\'s id is not valid! Go to <a href="index.php">Home</a></div>');
        }
    }
    else {
        die('<div class="Error">Participant\'s id is missing! Go to <a href="index.php">Home</a></div>');
    }
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>