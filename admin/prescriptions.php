<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_header.php';


$stmt = $pdo->query(
    "SELECT
        prescriptions.id,
        prescriptions.prescription_number,
        prescriptions.patient_name,
        prescriptions.doctor_name,
        prescriptions.issued_at,
        prescriptions.valid_until,
        prescriptions.status,

        GROUP_CONCAT(
            CONCAT(
                medicines.name,
                ' × ',
                prescription_items.quantity
            )
            ORDER BY medicines.name
            SEPARATOR ', '
        ) AS medicines

    FROM prescriptions

    LEFT JOIN prescription_items
        ON prescriptions.id = prescription_items.prescription_id

    LEFT JOIN medicines
        ON prescription_items.medicine_id = medicines.id

    GROUP BY
        prescriptions.id,
        prescriptions.prescription_number,
        prescriptions.patient_name,
        prescriptions.doctor_name,
        prescriptions.issued_at,
        prescriptions.valid_until,
        prescriptions.status

    ORDER BY prescriptions.created_at DESC"
);


$prescriptions = $stmt->fetchAll();

?>


<div class="page-header">

    <div>

        <h1>Receptes</h1>

        <p>
            Aptiekā reģistrēto recepšu pārvaldība.
        </p>

    </div>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>Reģistrētās receptes</h2>

            <p>
                Kopā:
                <strong>
                    <?= count($prescriptions) ?>
                </strong>
            </p>

        </div>

    </div>


    <div class="table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>Nr.</th>
                    <th>Pacients</th>
                    <th>Ārsts</th>
                    <th>Zāles</th>
                    <th>Izrakstīta</th>
                    <th>Derīga līdz</th>
                    <th>Statuss</th>
                </tr>

            </thead>


            <tbody>

                <?php foreach ($prescriptions as $prescription): ?>

                    <tr>

                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $prescription['prescription_number']
                                ) ?>
                            </strong>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $prescription['patient_name']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $prescription['doctor_name']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $prescription['medicines']
                                ?? 'Zāles nav norādītas'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $prescription['issued_at']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $prescription['valid_until']
                            ) ?>
                        </td>


                        <td>

                            <span
                                class="prescription-status
                                status-<?= strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $prescription['status']
                                    )
                                ) ?>"
                            >
                                <?= htmlspecialchars(
                                    $prescription['status']
                                ) ?>
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