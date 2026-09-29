<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_header.php';


$stmt = $pdo->query(
    'SELECT
        medicines.id,
        medicines.name,
        medicines.active_substance,
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


<div class="page-header">

    <div>

        <h1>Zāļu noliktava</h1>

        <p>
            Aptiekā pieejamo zāļu un to atlikumu pārvaldība.
        </p>

    </div>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>Zāles</h2>

            <p>
                Kopā:
                <strong>
                    <?= count($medicines) ?>
                </strong>
            </p>

        </div>

    </div>


    <div class="table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nosaukums</th>

                    <th>Kategorija</th>

                    <th>Aktīvā viela</th>

                    <th>Cena</th>

                    <th>Daudzums</th>

                    <th>Recepte</th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($medicines as $medicine): ?>

                    <tr>

                        <td>
                            #<?= (int) $medicine['id'] ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $medicine['name']
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $medicine['category_name']
                                ?? 'Bez kategorijas'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $medicine['active_substance']
                                ?? '-'
                            ) ?>

                        </td>


                        <td>

                            <?= number_format(
                                (float) $medicine['price'],
                                2,
                                ',',
                                ' '
                            ) ?>
                            €

                        </td>


                        <td>

                            <span
                                class="
                                    stock-value
                                    <?= $medicine['stock_quantity'] <= 10
                                        ? 'stock-low'
                                        : ''
                                    ?>
                                "
                            >

                                <?= (int) $medicine['stock_quantity'] ?>

                            </span>

                        </td>


                        <td>

                            <?php if ($medicine['prescription_required']): ?>

                                <span class="table-badge prescription-yes">
                                    Jā
                                </span>

                            <?php else: ?>

                                <span class="table-badge prescription-no">
                                    Nē
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>