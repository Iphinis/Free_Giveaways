<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
    if(isset($_GET["id_org"])) {
        $id_org = htmlspecialchars($_GET["id_org"]);
        // Vérification que l'id est bien un nombre et est positif
        if (is_numeric($id_org) && $id_org >= 0) {
            try {
                // Requête qui contient toutes les données nécessaires par la suite
                $req = $bdd->prepare("SELECT id_org, name, email FROM organizer WHERE id_org = :id_org");
                $req->bindValue(':id_org', $id_org, PDO::PARAM_INT);
                $req->execute();
                $dataOrg = $req->fetch();
                $row = $req->rowCount();

                // Si l'organisateur existe
                if ($row == 1) {
                    // Si connecté en tant qu'organisateur et même compte que le compte demandé alors afficher les informations privées si le compte organisateur est le même que celui de la session
                    if (isset($_SESSION['organizer']) && $_SESSION['organizer'][0] == $id_org) {
                        echo "<table class=\"giveaway\">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>{$dataOrg["name"]}</th>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th>{$dataOrg["email"]}</th>
                        </tr>
                        </thead>
                        </table>";
                    }
                    // Sinon afficher informations publiques si pas connecté en tant qu'organisateur (et donc soit connecté en tant que participant ou déconnecté)
                    else {
                        echo "<table class=\"giveaway\">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>{$dataOrg["name"]}</th>
                        </tr>
                        </thead>
                        </table>";
                    }

                    // Récupérer tous les concours de l'organisateur à tout le monde donc quel que soit le compte/ou déconnecté
                    $req = $bdd->prepare("SELECT id_conc, title, description, end_date FROM giveaway JOIN organizer ON giveaway.id_org = organizer.id_org WHERE organizer.id_org = :id_org");
                    $req->bindValue(':id_org', $id_org, PDO::PARAM_INT);
                    $req->execute();
                    $result = $req->fetchAll(PDO::FETCH_ASSOC);

                    if ($result != NULL) {
                        echo '<h1>' . $dataOrg['name'] . '\'s all giveaways:</h1>';
                        // Affichage de tous les concours créés par l'organisateur
                        foreach($result as $key => $row) {
                            strlen($row['description']) > 100 ? $small_description = substr($row['description'], 0, 100) . " [...]" : $small_description = $row['description'];
                            print_r("<div class=\"result\">
                                <a href=\"giveaway.php?id_conc={$row['id_conc']}\">{$row['title']}</a><br>
                                {$small_description}<br>
                                End at " . date("d/m/Y H:i:s", strtotime($row['end_date'])) . "</div>");
                        }
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
            die('<div class="Error">Organizer\'s id is not valid! Go to <a href="index.php">Home</a></div>');
        }
    }
    else {
        die('<div class="Error">Organizer\'s id is missing! Go to <a href="index.php">Home</a></div>');
    }
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>