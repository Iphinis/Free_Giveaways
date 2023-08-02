<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
if (isset($_SESSION['organizer']) || isset($_SESSION['participant'])) {
    die('You are already logged in... <a href="disconnect.php">Disconnect</a>');
}
elseif (isset($_POST['email']) && isset($_POST['password'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        // Si case cochée on se connecte en tant qu'organisateur
        if (isset($_POST['isOrganizer'])) {
            // Récupérer l'organisateur dans la base de données à partir de l'email
            $check = $bdd->prepare("SELECT id_org, name, email, password FROM organizer WHERE email = :email");
            $check->bindValue(':email', $email, PDO::PARAM_STR);
            $check->execute();
            $data = $check->fetch();
            $row = $check->rowCount();
        }
        // Sinon on se connecte en tant que participant
        else {
            // Récupérer le participant dans la base de données à partir de l'email
            $check = $bdd->prepare("SELECT id_part, name, email, password FROM participant WHERE email = :email");
            $check->bindValue(':email', $email, PDO::PARAM_STR);
            $check->execute();
            $data = $check->fetch();
            $row = $check->rowCount();
        }

        // Si email existe dans la base
        if ($row == 1) {
            // Si email dans le bon format
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $password = hash('sha256', $password);
                // Si mot de passe correspond à celui dans la base de données
                if ($data['password'] === $password) {
                    if (isset($_POST['isOrganizer']))
                        $_SESSION['organizer'] = [$data['id_org'],$data['name']];
                    else
                        $_SESSION['participant'] = [$data['id_part'],$data['name']];
                    header('Location:index.php');
                    die();
                }
                // Sinon mot de passe ne correspond pas
                else {
                    die('Email and password doesn\'t match!');
                }
            }
            // Sinon email dans le mauvais format
            else {
                die('Email must be valid!');
            }
        }
        // Sinon connexion échouée car email non trouvé dans la base de données
        else {
            die(isset($_POST['isOrganizer']) ? '[Organizer account] Connection failed: Email is incorrect!': '[Participant account] Connection failed: Email is incorrect!');
        }
}
else {
    echo '<form class="form" method="POST" action="login.php">
<fieldset>
<legend align="center">Login as Organizer/Participant</legend>
<label for="isOrganizer">Organizer Account</label>
<input name="isOrganizer" type="checkbox"><br>
<label for="email">E-mail</label>
<input name="email" id="email" type="email" placeholder="mail@example.com" aria-autocomplete="off" required/><br>
<label for="password">Password</label>
<input name="password" id="password" type="password" placeholder="********" required/><br>
</fieldset>

<input class="submit" type="submit" value="Login">
</form>
<div class="redirect"><a href="register.php">Not have an account yet?</a></div>';
}
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>