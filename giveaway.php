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
        try {
            // Récupérer les informations du concours
            $req = $bdd->prepare("SELECT organizer.id_org, name, title, description, start_date, end_date, winners FROM giveaway JOIN organizer ON giveaway.id_org = organizer.id_org WHERE id_conc = :id_conc");
            $req->bindValue(':id_conc', $id_conc, PDO::PARAM_INT);
            $req->execute();
            $data = $req->fetch();

            $date = date("Y-m-d H:i:s");
            if ($data['start_date'] <= $date && $data['end_date'] > $date) {
                $status = 'IN PROGRESS';
            }
            else if ($data['end_date'] < $date) {
                $status = 'ENDED';
            }
            else {
                $status = 'NOT STARTED YET';
            }
            
            echo "<h2>Status: {$status}</h2>
            <table class=\"giveaway\">
            <thead>
            <tr>
                <th>Title</th>
                <th>{$data["title"]}</th>
            </tr>
            <tr>
                <th>Description</th>
                <th>{$data["description"]}</th>
            </tr>
            <tr>
                <th>Start date</th>
                <th>{$data["start_date"]}</th>
            </tr>
            <tr>
                <th>End date</th>
                <th>{$data["end_date"]}</th>
            </tr>
            <tr>
                <th>Winners</th>
                <th>{$data["winners"]}</th>
            </tr>
            <tr>
                <th>Organizer</th>
                <th><a href=\"organizer.php?id_org={$data["id_org"]}\">{$data["name"]}</a></th>
            </tr>
            </thead>
            </table>";
            if ($status != 'ENDED') {
                if (isset($_SESSION['organizer']) && $_SESSION['organizer'][0] == $data['id_org']) {
                    echo '<a href="pickwinners.php?id_conc=' . $id_conc . '"><input class="submit" type="submit" value="Pick winners"></a>';
                }
                else if (isset($_SESSION['participant'])) {
                    echo '<a href="participate.php?id_conc=' . $id_conc . '">
                    <input class="submit" type="submit" value="Participate as “' . $_SESSION['participant'][1] . '”">
                    </a>
                    <h5>It is not you? <a href="disconnect.php">Disconnect</a></h5>';
                }
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e;
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