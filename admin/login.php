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
    <link
    rel="stylesheet"
    href="../assets/css/admin.css"
>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Autorizācija | Aptieka</title>

</head>

<body class="login-page">

    <main class="login-wrapper">

        <section class="login-card">

            <div class="login-brand">

                <span class="login-logo">
                    +
                </span>

                <div>

                    <h1>Aptieka</h1>

                    <p>
                        Pārvaldības sistēma
                    </p>

                </div>

            </div>


            <div class="login-heading">

                <h2>
                    Darbinieku autorizācija
                </h2>

                <p>
                    Ievadiet savus piekļuves datus.
                </p>

            </div>


            <?php if ($error !== ''): ?>

                <div class="login-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="login-form"
            >

                <div class="form-group">

                    <label for="username">
                        Lietotājvārds
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        autocomplete="username"
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Parole
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >

                </div>


                <button
                    type="submit"
                    class="login-button"
                >
                    Ielogoties
                </button>

            </form>

        </section>

    </main>

</body>

</html>