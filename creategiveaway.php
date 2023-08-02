<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
// Vérification que l'organisateur est connecté
if (isset($_SESSION['organizer']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['winners'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $start_date = htmlspecialchars($_POST['start_date']);
    $end_date = htmlspecialchars($_POST['end_date']);
    $winners = htmlspecialchars($_POST['winners']);
    if (strlen($title) > 2 && strlen($title) < 200) {
        if (strlen($description) > 0 && strlen($description) <= 1024) {
            if ($start_date > date("Y-m-d\TH:i")) {
                if ($end_date > $start_date) {
                    if ($winners > 0) {
                        try {
                            // Création du concours
                            $check = $bdd->prepare("INSERT INTO giveaway (id_org,title,description,start_date,end_date,winners) VALUES (:id_org,:title,:description,:start_date,:end_date,:winners)");
                            $check->bindValue(':id_org', $_SESSION['organizer'][0], PDO::PARAM_INT);
                            $check->bindValue(':title', $title, PDO::PARAM_STR);
                            $check->bindValue(':description', $description, PDO::PARAM_STR);
                            $check->bindValue(':start_date', date("Y-m-d\TH:i:s", strtotime($start_date)), PDO::PARAM_STR);
                            $check->bindValue(':end_date', date("Y-m-d\TH:i:s", strtotime($end_date)), PDO::PARAM_STR);
                            $check->bindValue(':winners', $winners, PDO::PARAM_INT);
                            $check->execute();
                        } catch (PDOException $e) {
                            echo 'Error: ' . $e;   
                        }
                        finally {
                            header('Location:index.php');
                        }
                    }
                    else {
                        createFilledForm();
                        die('<div class="error">Winners count must be superior to 0</div>');
                    }
                }
                else {
                    createFilledForm();
                    die('<div class="error">End date must be superior to Start date</div>');
                }
            }
            else {
                createFilledForm();
                die('<div class="error">Start date must be superior to current date</div>');
            }
        }
        else {
            createFilledForm();
            die('<div class="error">Description length must not be empty and must be inferior or equal to 1024 characters</div>');
        }
    }
    else {
        createFilledForm();
        die('<div class="error">Title length must be superior to 2 and inferior to 200 characters</div>');
    }
}
else if (empty($_SESSION['organizer']) || isset($_SESSION['participant'])) {
    die('<div class="error">You must be logged in as an organizer to create a giveaway! <a href="login.php">Login</a></div>');
}
else if (!empty($_SESSION['organizer']) && count($_POST) == 0) {
    echo '<form class="form" method="POST" action="creategiveaway.php">
    <fieldset>
        <legend align="center">Giveaway</legend>
        <label for="title">Title</label>
        <input name="title" id="title" type="text" placeholder="Keyboard giveaway" required/><br>

        <label for="description">Description</label>
        <textarea name="description" id="description" type="text" rows="5" cols="30" placeholder="I make a giveaway to win my keyboard that I used for 2 years..." required></textarea><br>

        <label for="start_date">Start date</label>
        <input name="start_date" id="start_date" id="start_date" type="datetime-local" required/><br>

        <label for="end_date">End date</label>
        <input name="end_date" id="end_date" id="end_date" type="datetime-local" required/><br>

        <label for="winners">Number of winner(s)</label>
        <input name="winners" id="winners" type="number" placeholder="1" min="1" required><br>
    </fieldset>
    <input class="submit" type="submit" value="Create a giveaway">
</form>';
}

function createFilledForm() {
    echo '<form class="form" method="POST" action="creategiveaway.php">
    <fieldset>
        <legend align="center">Giveaway</legend>
        <label for="title">Title</label>
        <input name="title" id="title" type="text" placeholder="Keyboard giveaway" value="' . htmlspecialchars($_POST['title']) .'" required/><br>

        <label for="description">Description</label>
        <textarea name="description" id="description" type="text" rows="5" cols="30" placeholder="I make a giveaway to win my keyboard that I used for 2 years..." required>' . htmlspecialchars($_POST['description']) .'</textarea><br>

        <label for="start_date">Start date</label>
        <input name="start_date" id="start_date" id="start_date" type="datetime-local" value="' . htmlspecialchars($_POST['start_date']) .'" required/><br>

        <label for="end_date">End date</label>
        <input name="end_date" id="end_date" id="end_date" type="datetime-local" value="' . htmlspecialchars($_POST['end_date']) .'" required/><br>

        <label for="winners">Number of winner(s)</label>
        <input name="winners" id="winners" type="number" placeholder="1" min="1" value="' . htmlspecialchars($_POST['winners']) .'" required><br>
    </fieldset>
    <input class="submit" type="submit" value="Create a giveaway">
</form>';
}
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>