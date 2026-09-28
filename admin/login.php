<?php

session_start();

require_once __DIR__ . '/../config/database.php';


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}


$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($username === '' || $password === '') {

        $error = 'Lūdzu, aizpildiet visus laukus.';

    } else {

        $stmt = $pdo->prepare(
            'SELECT
                id,
                username,
                password_hash,
                role
             FROM users
             WHERE username = ?
             LIMIT 1'
        );


        $stmt->execute([$username]);

        $user = $stmt->fetch();


        if (
            $user &&
            password_verify(
                $password,
                $user['password_hash']
            )
        ) {

            session_regenerate_id(true);

            $_SESSION['user_id'] =
                $user['id'];

            $_SESSION['username'] =
                $user['username'];

            $_SESSION['role'] =
                $user['role'];


            header('Location: dashboard.php');
            exit;

        } else {

            $error =
                'Nepareizs lietotājvārds vai parole.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="lv">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Autorizācija | Aptieka</title>

</head>

<body>

    <h1>Aptieka</h1>

    <h2>Darbinieku autorizācija</h2>


    <?php if ($error !== ''): ?>

        <p>
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <div>

            <label for="username">
                Lietotājvārds
            </label>

            <input
                type="text"
                id="username"
                name="username"
                required
            >

        </div>


        <div>

            <label for="password">
                Parole
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >

        </div>


        <button type="submit">
            Ielogoties
        </button>

    </form>

</body>

</html>