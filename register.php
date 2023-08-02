<!-- Connexion à la base de données -->
<?php require_once('templates/config.php'); ?>

<!-- Header -->
<?php require_once('templates/header.php'); ?>

<!-- Contenu de la page -->
<?php
if (isset($_SESSION['organizer']) || isset($_SESSION['participant'])) {
    die('You are already logged in... <a href="disconnect.php">Disconnect</a>');
}
// Si case cochée on s'enregistre en tant qu'organisateur
else if(isset($_POST['isOrganizer'])) {
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        // Récupérer l'organisateur dans la base de données à partir de l'email
        $check = $bdd->prepare("SELECT id_org, name, email, password FROM organizer WHERE email = :email");
        $check->bindValue(':email', $email, PDO::PARAM_STR);
        $check->execute();
        $data = $check->fetch();
        $row = $check->rowCount();
    
        // Si email n'existe pas dans la base
        if ($row == 0) {
            if (strlen($name) > 2 && strlen($name) <= 200) {
                // Si email dans le bon format
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $password = hash('sha256', $password);
    
                    // insertion de l'organisateur dans la base
                    try {
                        $insert = $bdd->prepare('INSERT INTO organizer(name, email, password) VALUES(:name,:email,:password)');
                        $insert->bindValue(':name', $name, PDO::PARAM_STR);
                        $insert->bindValue(':email', $email, PDO::PARAM_STR);
                        $insert->bindValue(':password', $password, PDO::PARAM_STR);
                        $insert->execute();
                        header('Location:index.php');
                    } catch (PDOException $e) {
                        echo 'Error: ' . $e;
                    }
                }
                // Sinon email dans le mauvais format
                else {
                    die('<div class="error">Email must be valid!</div>');
                }
            }
            else {
                die('<div class="error">Name length must be superior to 2 and inferior to 200 characters</div>');
            }
        }
        // Sinon inscription échouée car email trouvé dans la base de données
        else {
            die('<div class="error">Register failed: Email already exists!</div>');
        }
    }
    else {
        die('<div class="error">Register failed: All fields are required!</div>');
    }
}
// Sinon on s'enregistre en tant que participant
else if (empty($_POST['isOrganizer']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['address']) && isset($_POST['phone_number'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $address = htmlspecialchars($_POST['address']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    
    // Récupérer le participant dans la base de données à partir de l'email
    $check = $bdd->prepare("SELECT id_part, name, email, password FROM participant WHERE email = :email");
    $check->bindValue(':email', $email, PDO::PARAM_STR);
    $check->execute();
    $data = $check->fetch();
    $row = $check->rowCount();

    // Si email n'existe pas dans la base
    if ($row == 0) {
        if (strlen($name) > 2 && strlen($name) <= 200) {
            // Si email dans le bon format
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $password = hash('sha256', $password);
                if (strlen($address) >= 5) {
                    if (filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT)) {
                        // insertion du participant dans la base
                        try {
                            $insert = $bdd->prepare('INSERT INTO participant(name, email, password, address, phone_number) VALUES(:name,:email,:password, :address, :phone_number)');
                            $insert->execute(array(
                                'name' => $name,
                                'email' => $email,
                                'password' => $password,
                                'address' => $address,
                                'phone_number' => $phone_number
                            ));
                            header('Location:index.php');
                        } catch (PDOException $e) {
                            echo 'Error: ' . $e;
                        }
                    }
                    // Sinon numéro de téléphone dans le mauvais format
                    else {
                        die('<div class="error">Phone number must be valid!</div>');
                    }
                }
                // Sinon adresse trop courte
                else {
                    die('<div class="error">Address is not long enough!</div>');
                }
            }
            // Sinon email dans le mauvais format
            else {
                die('<div class="error">Email must be valid!</div>');
            }
        }
        else {
            die('<div class="error">Name length must be superior to 2 and inferior to 200 characters</div>');
        }
    }
    // Sinon inscription échouée car email trouvé dans la base de données
    else {
        die('<div class="error">Register failed: Email already exists!</div>');
    }
}
else {
    echo '<form class="form" method="POST" action="register.php">
<fieldset class="append">
<legend align="center">Register as Organizer/Participant</legend>
<label for="isOrganizer">Organizer Account</label>
<input name="isOrganizer" type="checkbox"><br>
<label for="name">Name</label>
<input name="name" id="name" type="text" placeholder="mail@example.com" required/><br>
<label for="email">E-mail</label>
<input name="email" id="email" type="email" placeholder="mail@example.com" required/><br>
<label for="password">Password</label>
<input name="password" id="password" type="password" placeholder="********" required/><br>
<label class="toremove" for="address">Address</label>
<input class="toremove" name="address" id="address" type="text" placeholder="123 Main Street, New York, NY 10030" required/><br class="toremove">
<label class="toremove" for="phone_number">Phone number</label>
<input class="toremove" name="phone_number" id="phone_number" type="text" placeholder="0612345678" required/><br class="toremove">
</fieldset>

<input class="submit" type="submit" value="Register">
</form>
<div class="redirect"><a href="login.php">Already have an account?</a></div>';
}
?>

<!-- Footer -->
<?php require_once('templates/footer.php'); ?>