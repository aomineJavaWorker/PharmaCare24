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

        medicines.image,

        categories.name AS category_name

     FROM medicines

     LEFT JOIN categories

        ON medicines.category_id = categories.id

     WHERE medicines.deleted_at IS NULL

     ORDER BY medicines.name ASC'

);

$medicines = $stmt->fetchAll();

/* Ieteiktā produktu izlase no esošajiem kataloga ierakstiem.
   Demonstrācijas izlase, nevis pārdošanas statistikas rangs. */
$featuredMedicines = [];
$featuredPriorities = ['Paracetamol', 'Ibuprofen', 'Vitamin C', 'No-Spa', 'Smecta'];
foreach ($featuredPriorities as $keyword) {
    foreach ($medicines as $item) {
        if ((int) $item['stock_quantity'] <= 0 || (int) $item['prescription_required'] === 1) {
            continue;
        }
        if (stripos($item['name'], $keyword) !== false) {
            $featuredMedicines[(int) $item['id']] = $item;
            break;
        }
    }
    if (count($featuredMedicines) >= 4) {
        break;
    }
}
if (count($featuredMedicines) < 4) {
    foreach ($medicines as $item) {
        if ((int) $item['stock_quantity'] > 0 && (int) $item['prescription_required'] !== 1) {
            $featuredMedicines[(int) $item['id']] = $item;
        }
        if (count($featuredMedicines) >= 4) {
            break;
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

    <title>PharmaCare24 | Zāļu demonstrācijas katalogs</title>
    <link
        rel="icon"
        type="image/png"
        href="assets/img/favicon.png"
    >
    <link

        rel="stylesheet"

        href="assets/css/style.css"

    >
    <link
        rel="stylesheet"
        href="assets/css/list-tools.css"
    >

    <link rel="stylesheet" href="assets/css/home-refresh.css">
</head>

<body>

<header class="header">

    <div class="container header-content">

        <a href="#sakums" class="logo">

            <span class="logo-icon">+</span>

            PharmaCare24

        </a>

        <nav id="mainNavigation" class="navigation">

            <a href="#sakums">

                Sākums

            </a>

            <a href="#par-mums">

                Par mums

            </a>

            <a href="#izlase">

                Izlase

            </a>

            <a href="#zales">

                Zāļu katalogs

            </a>

            <a href="#kontakti">

                Kontakti

            </a>

        </nav>

        <div class="header-actions">

            <button

                id="cartButton"

                class="cart-button"

                type="button"

                title="Grozs"

            >

                🛒

                <span

                    id="cartCount"

                    class="cart-count"

                >

                    0

                </span>

            </button>

            <button

                id="themeButton"

                class="theme-button"

                type="button"

                title="Tumšais režīms"

            >

                ☾

            </button>

            <button
                id="menuToggle"
                class="menu-toggle"
                type="button"
                aria-label="Atvērt izvēlni"
                aria-expanded="false"
                aria-controls="mainNavigation"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>

    </div>

</header>

<main>

    <!-- SĀKUMS -->
    <section id="sakums" class="hero home-hero">
        <div class="container hero-content">
            <div class="hero-text">
                <span class="home-eyebrow"><span class="home-eyebrow-dot" aria-hidden="true"></span> PHARMACARE24 · MĀCĪBU PROJEKTS</span>
                <h1>Vienkāršāks ceļš uz <span>skaidru informāciju.</span></h1>
                <p class="home-hero-description">
                    Atrodiet medikamentus demonstrācijas katalogā, salīdziniet cenas
                    un apskatiet produktu informāciju vienuviet.
                </p>
                <div class="home-hero-actions">
                    <a href="#zales" class="primary-button home-hero-primary">Atvērt katalogu <span aria-hidden="true">↗</span></a>
                    <a href="#izlase" class="home-hero-secondary">Apskatīt izlasi <span aria-hidden="true">→</span></a>
                </div>
                <div class="home-hero-highlights" aria-label="Kataloga iespējas">
                    <span><span aria-hidden="true">✓</span> Pārskatāmi filtri</span>
                    <span><span aria-hidden="true">✓</span> Produktu detaļas</span>
                    <span><span aria-hidden="true">✓</span> Gaišā un tumšā tēma</span>
                </div>
            </div>
            <div class="home-hero-visual">
                <div class="home-hero-image-wrap">
                    <img src="assets/img/pharmacy-hero.png"
                         alt="PharmaCare24 demonstrācijas aptiekas ilustrācija"
                         class="home-hero-image" fetchpriority="high">
                </div>
                <div class="home-hero-floating-card">
                    <span class="home-floating-icon" aria-hidden="true">✚</span>
                    <div>
                        <strong>Viss vienā katalogā</strong>
                        <small>Meklēšana · Cenas · Pieejamība</small>
                    </div>
                </div>
                <span class="home-hero-image-tag">Digitālas aptiekas demonstrācija</span>
            </div>
        </div>
    </section>

    <!-- PRODUKTU IZLASE -->
    <section id="izlase" class="section featured-section" aria-labelledby="featuredHeading">
        <div class="container">
            <div class="featured-intro">
                <div>
                    <span class="section-label">Populāru produktu izlase</span>
                    <h2 id="featuredHeading" class="section-title">Iepazīstiet pazīstamus produktus</h2>
                    <p>Atlasīti piemēri no demonstrācijas kataloga — nevis reālas pārdošanas statistikas tops.</p>
                </div>
                <a class="featured-all-link" href="#zales">Viss katalogs <span aria-hidden="true">↗</span></a>
            </div>

            <?php if (!empty($featuredMedicines)): ?>
                <div class="featured-grid">
                    <?php foreach ($featuredMedicines as $featured): ?>
                        <article class="featured-card">
                            <a class="featured-card-image"
                               href="medicine.php?id=<?= (int) $featured['id'] ?>"
                               aria-label="Skatīt <?= htmlspecialchars($featured['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (!empty($featured['image'])): ?>
                                    <img src="<?= htmlspecialchars($featured['image'], ENT_QUOTES, 'UTF-8') ?>"
                                         alt="<?= htmlspecialchars($featured['name'], ENT_QUOTES, 'UTF-8') ?> iepakojums"
                                         loading="lazy">
                                <?php else: ?>
                                    <span class="featured-placeholder" aria-hidden="true">✚</span>
                                <?php endif; ?>
                            </a>
                            <div class="featured-card-copy">
                                <span class="featured-category"><?= htmlspecialchars($featured['category_name'] ?? 'Bez kategorijas', ENT_QUOTES, 'UTF-8') ?></span>
                                <h3 class="featured-title">
                                    <a href="medicine.php?id=<?= (int) $featured['id'] ?>">
                                        <?= htmlspecialchars($featured['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <p class="featured-substance">
                                    <?= htmlspecialchars($featured['active_substance'] ?? 'Informācija pieejama produkta lapā', ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                            <div class="featured-bottom">
                                <div>
                                    <span class="featured-price-label">Cena</span>
                                    <strong class="featured-price"><?= number_format((float) $featured['price'], 2, ',', ' ') ?> €</strong>
                                </div>
                                <a class="featured-detail-link" href="medicine.php?id=<?= (int) $featured['id'] ?>"
                                   aria-label="Skatīt <?= htmlspecialchars($featured['name'], ENT_QUOTES, 'UTF-8') ?> detaļas">
                                    Skatīt <span aria-hidden="true">↗</span>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="featured-empty">Pašlaik nav pieejamu produktu izlasei. <a href="#zales">Atvērt visu katalogu</a></p>
            <?php endif; ?>
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

                Mācību projekts ar īstas lietotnes iespējām

            </h2>

            <p class="section-description">

                PharmaCare24 demonstrē, kā darbojas aptiekas tīmekļa lietotne:

                pārskatāms katalogs, produkta informācija un pasūtījumu plūsma.

                Vietne izveidota programmēšanas iemaņu demonstrēšanai.

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

                Ko var izmēģināt šajā vietnē?

            </h2>

            <div class="services-grid">

                <article class="service-card">

                    <div class="service-icon">

                        💊

                    </div>

                    <h3>

                        Produktu katalogs

                    </h3>

                    <p>

                        Meklēšana, kategoriju un cenu filtri,

                        kā arī ērta produktu atlase.

                    </p>

                </article>

                <article class="service-card">

                    <div class="service-icon">

                        ✓

                    </div>

                    <h3>

                        Detalizēti produktu apraksti

                    </h3>

                    <p>

                        Aktīvā viela, ražotājs, zāļu forma,

                        stiprums un informācijas avoti.

                    </p>

                </article>

                <article class="service-card">

                    <div class="service-icon">

                        ♡

                    </div>

                    <h3>

                        Demonstrācijas pasūtījumi

                    </h3>

                    <p>

                        Grozs, recepšu pārbaudes plūsma

                        un testa maksājuma integrācija.

                    </p>

                </article>

            </div>

        </div>

    </section>

        <!-- KĀPĒC PHARMACARE24 -->
    <section id="kapec" class="section benefits-section" aria-labelledby="benefitsHeading">
        <div class="container">
            <div class="benefits-intro">
                <span class="section-label">Pārdomāta lietošanas pieredze</span>
                <h2 id="benefitsHeading" class="section-title">Kāpēc izvēlēties PharmaCare24?</h2>
                <p>Trīs iemesli, kāpēc demonstrācijas katalogā ir vienkārši orientēties.</p>
            </div>
            <div class="benefits-grid">
                <article class="benefit-card">
                    <div class="benefit-topline">
                        <span class="benefit-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/><path d="M8 11h6M11 8v6"/></svg>
                        </span>
                        <span class="benefit-number">01</span>
                    </div>
                    <h3>Ātra produktu atrašana</h3>
                    <p>Meklējiet pēc nosaukuma, filtrējiet pēc kategorijas, cenas un pieejamības, kārtojiet rezultātus.</p>
                    <a href="#zales" class="benefit-inline-link">Izpētīt katalogu <span aria-hidden="true">→</span></a>
                </article>
                <article class="benefit-card">
                    <div class="benefit-topline">
                        <span class="benefit-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>
                        </span>
                        <span class="benefit-number">02</span>
                    </div>
                    <h3>Pārskatāma informācija</h3>
                    <p>Atsevišķā produkta lapā skatiet aktīvo vielu, ražotāju, formu, stiprumu un saiti uz oficiālo reģistru.</p>
                    <a href="#izlase" class="benefit-inline-link">Atvērt produktu izlasi <span aria-hidden="true">→</span></a>
                </article>
                <article class="benefit-card">
                    <div class="benefit-topline">
                        <span class="benefit-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="14" height="12" rx="2"/><path d="M8 21h8M12 17v4"/><rect x="18" y="10" width="4" height="10" rx="1"/></svg>
                        </span>
                        <span class="benefit-number">03</span>
                    </div>
                    <h3>Ērti jebkurā ierīcē</h3>
                    <p>Responsīvs dizains, gaišā un tumšā tēma un demonstrācijas grozs darbam datorā vai telefonā.</p>
                    <a href="#pakalpojumi" class="benefit-inline-link">Iespējas un funkcijas <span aria-hidden="true">→</span></a>
                </article>
            </div>
            <p class="demo-notice"><span aria-hidden="true">ⓘ</span> <strong>Demonstrācijas vietne:</strong> PharmaCare24 ir programmēšanas mācību projekts, nevis īsta aptieka. Šeit nevar iegādāties reālus medikamentus; neievadiet īstus personas datus.</p>
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

                Šajā sadaļā attēlotas aptiekā

                pieejamās zāles.

            </p>

            <div class="medicine-grid">

                <?php foreach ($medicines as $medicine): ?>

                    <article class="medicine-card">

                        <div class="medicine-image">

                            <?php if (!empty($medicine['image'])): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $medicine['image'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $medicine['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <span class="medicine-icon">
                                    +
                                </span>

                            <?php endif; ?>

                        </div>

                        <span class="medicine-category">

                            <?= htmlspecialchars(

                                $medicine['category_name']

                                ?? 'Bez kategorijas'

                            ) ?>

                        </span>

                        <h3>

                            <?= htmlspecialchars(

                                $medicine['name']

                            ) ?>

                        </h3>

                        <p class="medicine-substance">

                            <?= htmlspecialchars(

                                $medicine['active_substance']

                                ?? ''

                            ) ?>

                        </p>

                        <div class="medicine-info">

                            <span>

                                <?= htmlspecialchars(

                                    $medicine['dosage_form']

                                    ?? ''

                                ) ?>

                            </span>

                            <span>

                                <?= htmlspecialchars(

                                    $medicine['strength']

                                    ?? ''

                                ) ?>

                            </span>

                        </div>

                        <div class="medicine-status">

                            <?php if ($medicine['prescription_required']): ?>

                                <span

                                    class="

                                        prescription

                                        prescription-required

                                    "

                                >

                                    Recepšu zāles

                                </span>

                            <?php else: ?>

                                <span

                                    class="

                                        prescription

                                        prescription-free

                                    "

                                >

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

                        <a class="medicine-details-link" href="medicine.php?id=<?= (int) $medicine['id'] ?>">
                            Vairāk informācijas
                        </a>

                        <button

                            type="button"

                            class="medicine-buy-button"

                            data-medicine-id="<?= (int) $medicine['id'] ?>"

                            data-medicine-name="<?= htmlspecialchars(

                                $medicine['name'],

                                ENT_QUOTES,

                                'UTF-8'

                            ) ?>"

                            data-medicine-price="<?= htmlspecialchars(

                                (string) $medicine['price'],

                                ENT_QUOTES,

                                'UTF-8'

                            ) ?>"

                            data-medicine-stock="<?= (int) $medicine['stock_quantity'] ?>"

                            data-prescription-required="<?= (int) $medicine['prescription_required'] ?>"

                            <?= $medicine['stock_quantity'] <= 0

                                ? 'disabled'

                                : ''

                            ?>

                        >

                            <?= $medicine['stock_quantity'] > 0

                                ? 'Pirkt'

                                : 'Nav pieejams'

                            ?>

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

<!-- MEDICINE INFO MODAL -->

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

<!-- CART MODAL -->

<div

    id="cartModal"

    class="cart-modal"

>

    <div class="cart-modal-content">

        <div class="cart-header">

            <div>

                <span class="section-label">

                    Pirkums

                </span>

                <h2>

                    Jūsu grozs

                </h2>

            </div>

            <button

                type="button"

                id="cartCloseButton"

                class="cart-close-button"

                aria-label="Aizvērt"

            >

                ×

            </button>

        </div>

        <div

            id="cartMessage"

            class="cart-message"

        ></div>

        <div id="cartItems">

            <p class="cart-empty">

                Grozs ir tukšs.

            </p>

        </div>

        <div

            id="cartPrescriptionWarning"

            class="cart-prescription-warning"

        >

            Šajā grozā ir recepšu zāles.

            Pirms apmaksas būs nepieciešams

            pārbaudīt recepti.

        </div>

        <div class="cart-summary">

            <span>

                Kopā:

            </span>

            <strong id="cartTotal">

                0,00 €

            </strong>

        </div>

        <button

            type="button"

            id="continueCheckoutButton"

            class="cart-checkout-button"

        >

            Turpināt

        </button>

    </div>

</div>

<!-- PRESCRIPTION CHECK MODAL -->

<div

    id="prescriptionModal"

    class="cart-modal"

>

    <div class="cart-modal-content prescription-modal-content">

        <div class="cart-header">

            <div>

                <span class="section-label">

                    Receptes pārbaude

                </span>

                <h2>

                    Pircēja dati

                </h2>

            </div>

            <button

                type="button"

                id="prescriptionCloseButton"

                class="cart-close-button"

                aria-label="Aizvērt"

            >

                ×

            </button>

        </div>

        <p class="prescription-description">

            Grozā ir recepšu zāles.

            Lai turpinātu pirkumu,

            pārbaudiet recepti.

        </p>

        <div

            id="prescriptionCheckMessage"

            class="cart-message"

        ></div>

        <form id="prescriptionCheckForm">

            <div class="prescription-form-group">

                <label for="prescriptionPatientName">

                    Vārds un uzvārds *

                </label>

                <input

                    type="text"

                    id="prescriptionPatientName"

                    name="patient_name"

                    autocomplete="name"

                    required

                >

            </div>

            <div class="prescription-form-group">

                <label for="prescriptionPersonalCode">

                    Personas kods *

                </label>

                <input

                    type="text"

                    id="prescriptionPersonalCode"

                    name="personal_code"

                    placeholder="999999-00001"

                    maxlength="12"

                    pattern="[0-9]{6}-[0-9]{5}"

                    required

                >

            </div>

            <div class="prescription-actions">

                <button

                    type="button"

                    id="backToCartButton"

                    class="prescription-back-button"

                >

                    Atpakaļ

                </button>

                <button

                    type="submit"

                    class="cart-checkout-button prescription-check-button"

                >

                    Pārbaudīt recepti

                </button>

            </div>

        </form>

        <button

            type="button"

            id="prescriptionContinueButton"

            class="cart-checkout-button"

            disabled

        >

            Turpināt uz apmaksu

        </button>

    </div>

</div>

<footer class="footer">

    <div class="container footer-content">

        <div class="logo">

            <span class="logo-icon">+</span>

            PharmaCare24

        </div>

        <p>

            © 2026 PharmaCare24 · Mācību projekts

        </p>

    </div>

</footer>

<script src="assets/js/theme.js"></script>

<script src="assets/js/medicine-api.js"></script>

<script src="assets/js/cart.js"></script>

<script src="assets/js/mobile-menu.js"></script>

<script src="assets/js/list-tools.js"></script>

</body>

</html>