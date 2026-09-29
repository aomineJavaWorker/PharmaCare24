<?php

require_once __DIR__ . '/../includes/admin_header.php';

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

            <h2>
                Reģistrētās receptes
            </h2>

            <p>
                Kopā:
                <strong id="prescriptionCount">
                    0
                </strong>
            </p>

        </div>

    </div>


    <div
        id="prescriptionMessage"
        class="table-message"
    >
        Ielādē receptes...
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


            <tbody id="prescriptionTableBody">

            </tbody>

        </table>

    </div>

</section>


<script src="../assets/js/admin-prescriptions.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

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