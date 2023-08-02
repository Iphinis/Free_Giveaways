<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<form class="form" method="GET" action="searchgiveaway.php">
    <fieldset>
        <legend align="center">Search a Giveaway</legend>
        <input name="search" id="search" type="search" placeholder="Keyboard" required autocomplete="off"><br>
        <input class="submit" type="submit" value="Search">
    </fieldset>

    <fieldset>
        <legend align="center"><?php 
        echo isset($_GET["search"]) ? 'Result(s) of search "' . htmlspecialchars($_GET["search"]) . '"' : 'All Giveaway(s) in progress'; ?></legend>
        <?php
            if(empty($_GET['search'])) {
                try {
                    $req = $bdd->query('SELECT id_conc, title, description, start_date,end_date FROM giveaway WHERE start_date <= NOW() AND end_date > NOW()');
                    $req->execute();

                    // Afficher tous les concours en cours
                    while ($row = $req->fetch()) {
                        strlen($row['description']) > 100 ? $small_description = substr($row['description'], 0, 100) . " [...]" : $small_description = $row['description'];
                        print_r("<div class=\"result\">
                        <a href=\"giveaway.php?id_conc={$row['id_conc']}\">{$row['title']}</a><br>
                        “{$small_description}”<br>
                        End at " . date("d/m/Y H:i:s", strtotime($row['end_date'])) . "</div>");
                    }
                } catch (PDOException $e) {
                    echo 'Error: ' . $e;
                }
            }
            else {
                try {
                    $search = htmlspecialchars($_GET['search']);
                    $check = $bdd->prepare('SELECT DISTINCT id_conc, title, description, end_date FROM giveaway WHERE (title LIKE :search OR description LIKE :search)');
                    $check->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                    $check->execute();

                    // Afficher le(s) résultat(s) de la recherche
                    while ($data = $check->fetch()) {
                        strlen($data['description']) > 100 ? $small_description = substr($data['description'], 0, 100) . " [...]" : $small_description = $data['description'];
                        print_r("<div class=\"result\">
                            <a href=\"giveaway.php?id_conc={$data['id_conc']}\">{$data['title']}</a><br>
                            “{$small_description}”<br>
                            End at " . date("d/m/Y H:i:s", strtotime($data['end_date'])) . "</div>");
                    }
                    
                } catch (PDOException $e) {
                    echo 'Error: ' . $e;
                }
            }
        ?>
    </fieldset>
</form>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>