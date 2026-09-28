<?php

require_once __DIR__ . '/config/database.php';

$stmt = $pdo->query(
    'SELECT
        medicines.id,
        medicines.name,
        medicines.api_search_name,
        medicines.active_substance,
        medicines.manufacturer,
        medicines.dosage_form,
        medicines.strength,
        medicines.price,
        medicines.stock_quantity,
        medicines.prescription_required,
        categories.name AS category_name
    FROM medicines
    LEFT JOIN categories
        ON medicines.category_id = categories.id
    ORDER BY medicines.name ASC'
);

$medicines = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Aptieka</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >
</head>

<body>

<header class="header">

    <div class="container header-content">

        <a href="#sakums" class="logo">
            <span class="logo-icon">+</span>
            Aptieka
        </a>

        <nav class="navigation">

            <a href="#sakums">
                Sākums
            </a>

            <a href="#par-mums">
                Par mums
            </a>

            <a href="#pakalpojumi">
                Pakalpojumi
            </a>

            <a href="#zales">
                Zāļu katalogs
            </a>

            <a href="#kontakti">
                Kontakti
            </a>

        </nav>

        <button
            id="themeButton"
            class="theme-button"
            type="button"
            title="Tumšais režīms"
        >
            ☾
        </button>

    </div>

</header>


<main>

    <!-- SĀKUMS -->

    <section
        id="sakums"
        class="hero"
    >

        <div class="container hero-content">

            <div class="hero-text">

                <span class="section-label">
                    Jūsu veselībai
                </span>

                <h1>
                    Jūsu veselība —
                    mūsu prioritāte
                </h1>

                <p>
                    Kvalitatīvi medikamenti, pārskatāma
                    informācija un rūpes par jūsu veselību
                    vienuviet.
                </p>

                <a
                    href="#zales"
                    class="primary-button"
                >
                    Apskatīt zāles
                </a>

            </div>


            <div class="hero-card">

                <div class="pharmacy-symbol">
                    +
                </div>

                <h2>
                    Aptieka
                </h2>

                <p>
                    Veselība sākas ar rūpēm.
                </p>

            </div>

        </div>

    </section>


    <!-- PAR MUMS -->

    <section
        id="par-mums"
        class="section"
    >

        <div class="container">

            <span class="section-label">
                Par mums
            </span>

            <h2 class="section-title">
                Aptieka, kurai var uzticēties
            </h2>

            <p class="section-description">
                Mūsu mērķis ir nodrošināt klientiem
                ērtu piekļuvi medikamentiem un
                noderīgai informācijai par veselību.
            </p>

        </div>

    </section>


    <!-- PAKALPOJUMI -->

    <section
        id="pakalpojumi"
        class="section section-light"
    >

        <div class="container">

            <span class="section-label">
                Pakalpojumi
            </span>

            <h2 class="section-title">
                Ko mēs piedāvājam?
            </h2>


            <div class="services-grid">

                <article class="service-card">

                    <div class="service-icon">
                        💊
                    </div>

                    <h3>
                        Plašs sortiments
                    </h3>

                    <p>
                        Recepšu un bezrecepšu zāles,
                        vitamīni un citi veselības
                        produkti.
                    </p>

                </article>


                <article class="service-card">

                    <div class="service-icon">
                        ✓
                    </div>

                    <h3>
                        Kvalitatīvi produkti
                    </h3>

                    <p>
                        Aptiekā pieejami pārbaudīti un
                        kvalitatīvi veselības produkti.
                    </p>

                </article>


                <article class="service-card">

                    <div class="service-icon">
                        ♡
                    </div>

                    <h3>
                        Rūpes par klientu
                    </h3>

                    <p>
                        Ērta un pārskatāma informācija
                        par aptiekā pieejamajiem
                        produktiem.
                    </p>

                </article>

            </div>

        </div>

    </section>


    <!-- ZĀĻU KATALOGS -->

    <section
        id="zales"
        class="section"
    >

        <div class="container">

            <span class="section-label">
                Katalogs
            </span>

            <h2 class="section-title">
                Zāļu katalogs
            </h2>

            <p class="section-description">
                Šajā sadaļā tiks attēlotas aptiekā
                pieejamās zāles.
            </p>


            <div class="medicine-grid">

        <?php foreach ($medicines as $medicine): ?>

            <article class="medicine-card">

                <div class="medicine-image">
                    <span class="medicine-icon">+</span>
                </div>

                <span class="medicine-category">

                    <?= htmlspecialchars(
                        $medicine['category_name'] ?? 'Bez kategorijas'
                    ) ?>

                </span>


                <h3>
                    <?= htmlspecialchars($medicine['name']) ?>
                </h3>


                <p class="medicine-substance">

                    <?= htmlspecialchars(
                        $medicine['active_substance'] ?? ''
                    ) ?>

                </p>


                <div class="medicine-info">

                    <span>
                        <?= htmlspecialchars(
                            $medicine['dosage_form'] ?? ''
                        ) ?>
                    </span>

                    <span>
                        <?= htmlspecialchars(
                            $medicine['strength'] ?? ''
                        ) ?>
                    </span>

                </div>


                <div class="medicine-status">

                    <?php if ($medicine['prescription_required']): ?>

                        <span class="prescription prescription-required">
                            Recepšu zāles
                        </span>

                    <?php else: ?>

                        <span class="prescription prescription-free">
                            Bez receptes
                        </span>

                    <?php endif; ?>


                    <?php if ($medicine['stock_quantity'] > 0): ?>

                        <span class="stock available">
                            Pieejams
                        </span>

                    <?php else: ?>

                        <span class="stock unavailable">
                            Nav pieejams
                        </span>

                    <?php endif; ?>

                </div>


                <div class="medicine-bottom">

                    <strong class="medicine-price">

                        <?= number_format(
                            (float) $medicine['price'],
                            2,
                            ',',
                            ' '
                        ) ?>
                        €

                    </strong>

                    <small>
                        Noliktavā:
                        <?= (int) $medicine['stock_quantity'] ?>
                    </small>

                </div>
                    <button
                        type="button"
                        class="medicine-info-button"
                        data-medicine-name="<?= htmlspecialchars($medicine['name']) ?>"
                        data-active-substance="<?= htmlspecialchars($medicine['active_substance'] ?? '') ?>"
                        data-api-search-name="<?= htmlspecialchars(
                        $medicine['api_search_name'] ?? '') ?>"
                    >
                        Vairāk informācijas
                    </button>

            </article>

        <?php endforeach; ?>

    </div>

        </div>

    </section>


    <!-- KONTAKTI -->

    <section
        id="kontakti"
        class="section section-light"
    >

        <div class="container">

            <span class="section-label">
                Kontakti
            </span>

            <h2 class="section-title">
                Sazinieties ar mums
            </h2>


            <div class="contact-grid">

                <div class="contact-card">

                    <span>
                        Adrese
                    </span>

                    <strong>
                        Brīvības iela 123, Latvija
                    </strong>

                </div>


                <div class="contact-card">

                    <span>
                        Tālrunis
                    </span>

                    <strong>
                        +371 20 000 000
                    </strong>

                </div>


                <div class="contact-card">

                    <span>
                        E-pasts
                    </span>

                    <strong>
                        info@aptieka.lv
                    </strong>

                </div>


                <div class="contact-card">

                    <span>
                        Darba laiks
                    </span>

                    <strong>
                        P.–Pk. 08:00–20:00
                    </strong>

                </div>

            </div>

        </div>

    </section>

</main>

<div
    id="medicineModal"
    class="medicine-modal"
>

    <div class="medicine-modal-content">

        <button
            type="button"
            id="medicineModalClose"
            class="medicine-modal-close"
            aria-label="Aizvērt"
        >
            ×
        </button>


        <div id="medicineModalBody">

            <p>
                Ielādē informāciju...
            </p>

        </div>

    </div>

</div>

<footer class="footer">

    <div class="container footer-content">

        <div class="logo">
            <span class="logo-icon">+</span>
            Aptieka
        </div>

        <p>
            © 2026 Aptieka
        </p>

    </div>

</footer>
<script src="assets/js/theme.js"></script>
<script src="assets/js/medicine-api.js"></script>
</body>

</html>