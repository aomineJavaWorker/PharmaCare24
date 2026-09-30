<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_header.php';


$medicineCount = (int) $pdo
    ->query(
        'SELECT COUNT(*)
         FROM medicines
         WHERE deleted_at IS NULL'
    )
    ->fetchColumn();


$activePrescriptionCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM prescriptions
        WHERE status = 'Aktīva'
        AND deleted_at IS NULL
    ")
    ->fetchColumn();


$newOrderCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM orders
        WHERE status = 'Jauns'
        AND deleted_at IS NULL
    ")
    ->fetchColumn();


$lowStockCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM medicines
        WHERE stock_quantity <= 10
        AND deleted_at IS NULL")
            ->fetchColumn();


$latestOrdersStmt = $pdo->query("
    SELECT
        id,
        customer_name,
        total,
        status,
        created_at
    FROM orders
    WHERE deleted_at IS NULL
    ORDER BY created_at DESC
    LIMIT 5
");

$latestOrders = $latestOrdersStmt->fetchAll();

?>


<div class="dashboard-header">

    <div>

        <h1>
            Kopsavilkums
        </h1>

        <p>
            Aktuālā informācija par aptiekas pārvaldības sistēmu.
        </p>

    </div>

</div>


<div class="stats-grid">

    <article class="stat-card">

        <span>
            Zāles noliktavā
        </span>

        <strong>
            <?= $medicineCount ?>
        </strong>

    </article>


    <article class="stat-card">

        <span>
            Aktīvās receptes
        </span>

        <strong>
            <?= $activePrescriptionCount ?>
        </strong>

    </article>


    <article class="stat-card">

        <span>
            Jaunie pasūtījumi
        </span>

        <strong>
            <?= $newOrderCount ?>
        </strong>

    </article>


    <article class="stat-card">

        <span>
            Zems atlikums
        </span>

        <strong>
            <?= $lowStockCount ?>
        </strong>

    </article>

</div>


<section class="dashboard-panel">

    <div class="dashboard-panel-header">

        <div>

            <h2>
                Jaunākie pasūtījumi
            </h2>

            <p>
                Pēdējie pieci sistēmā reģistrētie pasūtījumi.
            </p>

        </div>


        <a
            href="orders.php"
            class="secondary-button"
        >
            Skatīt visus
        </a>

    </div>


    <div class="table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Klients</th>

                    <th>Datums</th>

                    <th>Summa</th>

                    <th>Statuss</th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($latestOrders as $order): ?>

                    <tr>

                        <td>
                            #<?= (int) $order['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($order['customer_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($order['created_at']) ?>
                        </td>

                        <td>
                            <?= number_format(
                                (float) $order['total'],
                                2,
                                ',',
                                ' '
                            ) ?>
                            €
                        </td>

                        <td>

                            <span class="status-badge">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>

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