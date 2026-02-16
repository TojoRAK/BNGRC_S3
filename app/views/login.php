<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>

<h2>Connexion</h2>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!empty($_SESSION['flash_error'])) {
    echo "<p style='color:red'>" . $_SESSION['flash_error'] . "</p>";
    unset($_SESSION['flash_error']);
}
?>

<form method="POST" action="/login">

    <label>Email :</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="password" required><br><br>

    <label>Se connecter en tant que :</label><br>
    <select name="role" required>
        <option value="">-- Choisir --</option>
        <option value="CLIENT">Client</option>
        <option value="ADMIN">Admin</option>
    </select><br><br>

    <button type="submit">Se connecter</button>
</form>

<hr>

<p><strong>Admin par défaut si aucun admin en base :</strong><br>
Email: admin@test.com<br>
Password: admin123<br>
Role: ADMIN
</p>

</body>
</html>
