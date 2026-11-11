<?php
require_once __DIR__ . '/config/database.php';

function h($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$medicine = null;

if ($id !== false && $id !== null) {
    $stmt = $pdo->prepare(
        'SELECT m.id, m.name, m.active_substance, m.manufacturer,
                m.dosage_form, m.strength, m.price, m.stock_quantity,
                m.prescription_required, m.image,
                c.name AS category_name
         FROM medicines m
         LEFT JOIN categories c ON c.id = m.category_id
         WHERE m.id = ? AND m.deleted_at IS NULL
         LIMIT 1'
    );
    $stmt->execute([$id]);
    $medicine = $stmt->fetch() ?: null;
}

if (!$medicine) {
    http_response_code(404);
}

$available = $medicine && (int) $medicine['stock_quantity'] > 0;
$name = $medicine['name'] ?? 'Zāles nav atrastas';
$registryUrl = 'https://zva.gov.lv/lv/pacientiem-un-sabiedribai/zales/zalu-iegade/zalu-registrs';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Informācija par <?= h($name) ?> PharmaCare24 demonstrācijas katalogā.">
    <title><?= h($name) ?> | PharmaCare24</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/medicine-detail.css">
</head>
<body>
<header class="header">
    <div class="container header-content">
        <a href="index.php#sakums" class="logo">
            <span class="logo-icon">+</span> PharmaCare24
        </a>
        <nav id="mainNavigation" class="navigation" aria-label="Galvenā navigācija">
            <a href="index.php#sakums">Sākums</a>
            <a href="index.php#izlase">Kataloga izlase</a>
            <a href="index.php#zales">Zāļu katalogs</a>
            <a href="index.php#kontakti">Kontakti</a>
        </nav>
        <div class="header-actions">
            <button id="themeButton" class="theme-button" type="button" title="Mainīt tēmu" aria-label="Mainīt tēmu">☾</button>
            <button id="menuToggle" class="menu-toggle" type="button" aria-label="Atvērt izvēlni"
                    aria-expanded="false" aria-controls="mainNavigation">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main class="detail-page">
    <div class="container">
        <nav class="detail-breadcrumbs" aria-label="Lapas ceļš">
            <a href="index.php#sakums">Sākums</a><span>/</span>
            <a href="index.php#zales">Zāļu katalogs</a><span>/</span>
            <span aria-current="page"><?= h($name) ?></span>
        </nav>

        <?php if (!$medicine): ?>
            <section class="detail-not-found">
                <span class="section-label">404</span>
                <h1>Zāles nav atrastas</h1>
                <p>Šis produkts nav pieejams demonstrācijas katalogā.</p>
                <a class="detail-primary" href="index.php#zales">Atgriezties katalogā</a>
            </section>
        <?php else: ?>
            <div class="detail-layout">
                <div class="detail-picture">
                    <?php if (!empty($medicine['image'])): ?>
                        <img src="<?= h($medicine['image']) ?>" alt="<?= h($name) ?> iepakojums">
                    <?php else: ?>
                        <div class="detail-placeholder" aria-label="Nav attēla">+</div>
                    <?php endif; ?>
                </div>

                <section class="detail-summary" aria-labelledby="medicineTitle">
                    <span class="section-label"><?= h($medicine['category_name'] ?: 'Bez kategorijas') ?></span>
                    <h1 id="medicineTitle"><?= h($name) ?></h1>
                    <p class="detail-lead"><?= h($medicine['active_substance'] ?: 'Aktīvā viela nav norādīta') ?></p>
                    <div class="detail-badges">
                        <span class="prescription <?= (int) $medicine['prescription_required'] === 1 ? 'prescription-required' : 'prescription-free' ?>">
                            <?= (int) $medicine['prescription_required'] === 1 ? 'Recepšu zāles' : 'Bez receptes' ?>
                        </span>
                        <span class="stock <?= $available ? 'available' : 'unavailable' ?>">
                            <?= $available ? 'Pieejams' : 'Nav pieejams' ?>
                        </span>
                    </div>

                    <div class="detail-buy-panel">
                        <div class="detail-buy-price">
                            <span class="detail-small-label">Cena</span>
                            <strong class="detail-price"><?= number_format((float) $medicine['price'], 2, ',', ' ') ?> €</strong>
                            <span class="detail-quantity">Noliktavā: <?= (int) $medicine['stock_quantity'] ?></span>
                        </div>
                        <?php if ($available): ?>
                            <div class="detail-buy-controls">
                                <label for="detailQty">Daudzums</label>
                                <input id="detailQty" type="number" value="1" min="1"
                                       max="<?= (int) $medicine['stock_quantity'] ?>" step="1" inputmode="numeric">
                                <button id="detailAddCart" type="button" class="detail-primary"
                                        data-id="<?= (int) $medicine['id'] ?>"
                                        data-name="<?= h($name) ?>"
                                        data-price="<?= h($medicine['price']) ?>"
                                        data-stock="<?= (int) $medicine['stock_quantity'] ?>"
                                        data-prescription="<?= (int) $medicine['prescription_required'] ?>">
                                    Pievienot grozam
                                </button>
                            </div>
                        <?php else: ?>
                            <span class="detail-primary detail-disabled">Nav pieejams</span>
                        <?php endif; ?>
                    </div>
                    <p class="detail-feedback" id="detailFeedback" role="status" aria-live="polite"></p>
                    <a href="index.php#zales" class="detail-back-link">← Atpakaļ uz katalogu un grozu</a>

                    <div class="detail-attributes">
                        <h2>Informācija par produktu</h2>
                        <dl>
                            <div><dt>Aktīvā viela</dt><dd><?= h($medicine['active_substance'] ?: 'Nav norādīta') ?></dd></div>
                            <div><dt>Ražotājs</dt><dd><?= h($medicine['manufacturer'] ?: 'Nav norādīts') ?></dd></div>
                            <div><dt>Zāļu forma</dt><dd><?= h($medicine['dosage_form'] ?: 'Nav norādīta') ?></dd></div>
                            <div><dt>Stiprums</dt><dd><?= h($medicine['strength'] ?: 'Nav norādīts') ?></dd></div>
                            <div><dt>Kategorija</dt><dd><?= h($medicine['category_name'] ?: 'Bez kategorijas') ?></dd></div>
                        </dl>
                    </div>
                </section>
            </div>

            <section class="detail-official">
                <div>
                    <span class="section-label">Oficiāla informācija</span>
                    <h2>Lietošanas instrukcija un pilns sastāvs</h2>
                    <p>
                        Šajā demonstrācijas lapā ir tikai datubāzē saglabātie produkta pamatdati.
                        Pilnu sastāvu, lietošanas instrukciju, kontrindikācijas un mijiedarbību
                        pārbaudiet Latvijas Zāļu reģistrā pēc konkrētā preparāta nosaukuma.
                    </p>
                    <p class="detail-disclaimer">PharmaCare24 ir mācību projekts, nevis īsta aptieka.
                        Cena un atlikums ir demonstrācijas dati. Neievadiet reālus personas datus.</p>
                </div>
                <a href="<?= h($registryUrl) ?>" target="_blank" rel="noopener noreferrer"
                   class="detail-secondary">ZVA zāļu reģistrs ↗</a>
            </section>
        <?php endif; ?>
    </div>
</main>
<footer class="footer">
    <div class="container footer-content">
        <div class="logo"><span class="logo-icon">+</span> PharmaCare24</div>
        <p>© 2026 PharmaCare24 · Mācību projekts</p>
    </div>
</footer>
<script src="assets/js/theme.js"></script>
<script src="assets/js/mobile-menu.js"></script>
<script src="assets/js/medicine-detail.js"></script>
</body>
</html>
