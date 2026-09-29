<?php

require_once __DIR__ . '/../includes/admin_header.php';

?>


<div class="page-header">

    <div>
        <h1>Zāļu noliktava</h1>
        <p>Aptiekā pieejamo zāļu un to atlikumu pārvaldība.</p>
    </div>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>
            <h2>Zāles</h2>

            <p>
                Kopā:
                <strong id="medicineCount">0</strong>
            </p>
        </div>

    </div>


    <div id="medicineMessage" class="table-message">
        Ielādē zāles...
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

            <tbody id="medicineTableBody"></tbody>

        </table>

    </div>

</section>


<script src="../assets/js/admin-medicines.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>