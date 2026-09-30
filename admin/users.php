<?php

require_once __DIR__ . '/../includes/admin_auth.php';

requireMainAdminPage();

require_once __DIR__ . '/../includes/admin_header.php';

?>


<div class="page-header">

    <div>

        <h1>
            Lietotāji
        </h1>

        <p>
            Pārvaldības sistēmas lietotāju pārvaldība.
        </p>

    </div>


    <button
        type="button"
        id="openUserModal"
        class="primary-admin-button"
    >
        + Pievienot moderatoru
    </button>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Sistēmas lietotāji
            </h2>

            <p>
                Kopā:
                <strong id="userCount">
                    0
                </strong>
            </p>

        </div>

    </div>


    <div
        id="userMessage"
        class="table-message"
    >
        Ielādē lietotājus...
    </div>


    <div class="table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Lietotājvārds</th>
                    <th>Loma</th>
                    <th>Izveidots</th>
                    <th>Atjaunināts</th>
                </tr>

            </thead>


            <tbody id="userTableBody">
            </tbody>

        </table>

    </div>

</section>


<!-- MODAL -->

<div
    id="userModal"
    class="admin-modal"
>

    <div class="admin-modal-content">


        <div class="admin-modal-header">

            <div>

                <h2>
                    Pievienot moderatoru
                </h2>

                <p>
                    Izveidojiet jaunu moderatora kontu.
                </p>

            </div>


            <button
                type="button"
                id="closeUserModal"
                class="admin-modal-close"
            >
                ×
            </button>

        </div>


        <form id="userForm">


            <div
                id="userFormMessage"
                class="form-message"
            ></div>


            <div class="form-grid">


                <div class="form-group">

                    <label for="newUsername">
                        Lietotājvārds *
                    </label>

                    <input
                        type="text"
                        id="newUsername"
                        name="username"
                        minlength="3"
                        maxlength="50"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="newPassword">
                        Parole *
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="password"
                        minlength="8"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirmPassword">
                        Atkārtot paroli *
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="password_confirm"
                        minlength="8"
                        required
                    >

                </div>


            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    id="cancelUserModal"
                    class="secondary-button"
                >
                    Atcelt
                </button>


                <button
                    type="submit"
                    class="primary-admin-button"
                >
                    Izveidot moderatoru
                </button>

            </div>


        </form>

    </div>

</div>


<script src="../assets/js/admin-users.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>