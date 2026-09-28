<?php

require_once __DIR__ . '/admin_auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="lv">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Aptieka | Administrācija</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>

<body>


<div class="admin-layout">


    <!-- SIDEBAR -->

    <aside class="admin-sidebar">

        <a
            href="dashboard.php"
            class="admin-logo"
        >
            <span>+</span>

            Aptieka
        </a>


        <nav class="admin-navigation">

            <a
                href="dashboard.php"
                class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"
            >
                Kopsavilkums
            </a>


            <a
                href="medicines.php"
                class="<?= $currentPage === 'medicines.php' ? 'active' : '' ?>"
            >
                Zāļu noliktava
            </a>


            <a
                href="prescriptions.php"
                class="<?= $currentPage === 'prescriptions.php' ? 'active' : '' ?>"
            >
                Receptes
            </a>


            <a
                href="orders.php"
                class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>"
            >
                Pasūtījumi
            </a>

        </nav>

    </aside>


    <!-- RIGHT SIDE -->

    <div class="admin-page">


        <header class="admin-topbar">

            <div>

                <strong>
                    Vadības panelis
                </strong>

            </div>


            <div class="admin-user">

                <div>

                    <span>
                        Sveiki,
                        <?= htmlspecialchars($_SESSION['username']) ?>!
                    </span>

                    <small>
                        <?= htmlspecialchars($_SESSION['role']) ?>
                    </small>

                </div>


                <a
                    href="logout.php"
                    class="logout-button"
                >
                    Iziet
                </a>

            </div>

        </header>


        <main class="admin-main">

            <div class="admin-content">