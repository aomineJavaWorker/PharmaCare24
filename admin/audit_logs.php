<?php

require_once __DIR__ . '/../includes/admin_auth.php';

requireMainAdminPage();

require_once __DIR__ . '/../includes/admin_header.php';

?>


<div class="page-header">

    <div>

        <h1>
            Darbību žurnāls
        </h1>

        <p>
            Pārvaldības sistēmā veikto darbību vēsture.
        </p>

    </div>

</div>


<section class="admin-panel">

    <div
        id="auditMessage"
        class="table-message"
    >
        Ielādē darbību žurnālu...
    </div>


    <div class="table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>Datums</th>
                    <th>Lietotājs</th>
                    <th>Loma</th>
                    <th>Darbība</th>
                    <th>Objekts</th>
                    <th>ID</th>
                    <th>Informācija</th>
                    <th>IP</th>
                </tr>

            </thead>


            <tbody id="auditTableBody">
            </tbody>

        </table>

    </div>

</section>


<script src="../assets/js/admin-audit-logs.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>