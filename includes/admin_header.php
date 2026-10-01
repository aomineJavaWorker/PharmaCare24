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

    <link
        rel="stylesheet"
        href="../assets/css/list-tools.css"
    >

    <script
        src="../assets/js/admin-mobile-menu.js"
        defer
    ></script>

    <script
        src="../assets/js/list-tools.js"
        defer
    ></script>

    <script>
        window.addEventListener(
            'pageshow',
            function (event) {

                if (event.persisted) {
                    window.location.reload();
                }
            }
        );
    </script>

</head>

<body>


<div class="admin-layout">


    <!-- SIDEBAR -->

    <aside
        class="admin-sidebar"
        id="adminSidebar"
    >

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


            <?php if (isMainAdmin()): ?>

                <a
                    href="users.php"
                    class="<?= $currentPage === 'users.php' ? 'active' : '' ?>"
                >
                    Lietotāji
                </a>


                <a
                    href="audit_logs.php"
                    class="<?= $currentPage === 'audit_logs.php' ? 'active' : '' ?>"
                >
                    Darbību žurnāls
                </a>

            <?php endif; ?>

        </nav>

    </aside>


    <!-- MOBILE BACKDROP -->

    <div
        class="admin-sidebar-backdrop"
        id="adminSidebarBackdrop"
    ></div>


    <!-- RIGHT SIDE -->

    <div class="admin-page">


        <header class="admin-topbar">


            <div class="admin-topbar-left">

                <button
                    type="button"
                    class="admin-menu-toggle"
                    id="adminMenuToggle"
                    aria-label="Atvērt izvēlni"
                    aria-expanded="false"
                    aria-controls="adminSidebar"
                >

                    <span></span>
                    <span></span>
                    <span></span>

                </button>


                <strong>
                    Vadības panelis
                </strong>

            </div>


            <div class="admin-user">

                <div>

                    <span>
                        Sveiki,
                        <?= htmlspecialchars(
                            $_SESSION['username']
                        ) ?>!
                    </span>


                    <small>

                        <?= isMainAdmin()
                            ? 'Galvenais administrators'
                            : 'Moderators'
                        ?>

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