<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<h1><u>Free Giveaways</u></h1>
<h2>Here you can win everything for free!</h2>
<div class="giveaways">
    <h1><u>Popular Giveaways:</u><h1>
    <?php
        // Afficher les giveaways ayant le plus de participants
        try {
            $req = $bdd->query('SELECT COUNT(id_part) as RANK, giveaway.id_conc, title, description, start_date, end_date FROM giveaway JOIN participate ON giveaway.id_conc = participate.id_conc WHERE start_date <= NOW() AND end_date > NOW() ORDER BY RANK DESC LIMIT 5');
            $req->execute();

            $result = $req->fetchAll(PDO::FETCH_ASSOC);

            if (isset($result[0]['description'])) {
                foreach($result as $key => $row) {
                    strlen($row['description']) > 100 ? $small_description = substr($row['description'], 0, 100) . " [...]" : $small_description = $row['description'];
                    print_r("<div>
                        <h3><a href=\"giveaway.php?id_conc={$row['id_conc']}\">{$row['title']}</a></h3><br>
                        <h4>{$small_description}<br></h4>
                        <h5>End at " . date("d/m/Y H:i:s", strtotime($row['end_date'])) . "</h5></div><br>");
                }
            }
            else {
                echo '<h4>No giveaway has been found for the moment</h4>';
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e;
        }
    ?>
</div>
<div class="giveaways">
    <h1><u>Recent Giveaways:</u><h1>
    <?php
        // Afficher les giveaways les plus récents
        try {
            $req = $bdd->query('SELECT id_conc, title, description, start_date, end_date FROM giveaway WHERE start_date <= NOW() AND end_date > NOW() ORDER BY id_conc DESC LIMIT 5');
            $req->execute();

            $result = $req->fetchAll(PDO::FETCH_ASSOC);

            if (isset($result[0]['description'])) {
                foreach($result as $key => $row) {
                    strlen($row['description']) > 100 ? $small_description = substr($row['description'], 0, 100) . " [...]" : $small_description = $row['description'];
                    print_r("<div>
                        <h3><a href=\"giveaway.php?id_conc={$row['id_conc']}\">{$row['title']}</a></h3><br>
                        <h4>{$small_description}<br></h4>
                        <h5>End at " . date("d/m/Y H:i:s", strtotime($row['end_date'])) . "</h5></div><br>");
                }
            }
            else {
                echo '<h4>No giveaway has been found for the moment</h4>';
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e;
        }
    ?>
</div>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>