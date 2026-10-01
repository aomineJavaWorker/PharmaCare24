<?php

require_once __DIR__ . '/../includes/admin_auth.php';

requireMainAdminPage();

require_once __DIR__ . '/../includes/admin_header.php';

?>


<div class="page-header">

    <div>

        <h1>Lietotāji</h1>

        <p>
            Pārvaldības sistēmas lietotāju pārvaldība.
        </p>

    </div>

    <button
        type="button"
        id="openUserModal"
        class="primary-admin-button"
    >
        + Pievienot lietotāju
    </button>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>Sistēmas lietotāji</h2>

            <p>
                Kopā:
                <strong id="userCount">0</strong>
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

        <table class="admin-table actions-table users-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Lietotājvārds</th>
                    <th>Loma</th>
                    <th>Izveidots</th>
                    <th>Atjaunināts</th>
                    <th>Darbības</th>
                </tr>

            </thead>


            <tbody id="userTableBody">
            </tbody>

        </table>

    </div>

</section>


<!-- LIETOTĀJA MODĀLAIS LOGS -->

<div
    id="userModal"
    class="admin-modal"
>

    <div class="admin-modal-content">

        <div class="admin-modal-header">

            <div>

                <h2 id="userModalTitle">
                    Pievienot lietotāju
                </h2>

                <p id="userModalDescription">
                    Izveidojiet jaunu sistēmas lietotāju.
                </p>

            </div>

            <button
                type="button"
                id="closeUserModal"
                class="admin-modal-close"
                aria-label="Aizvērt"
            >
                ×
            </button>

        </div>


        <form id="userForm">

            <input
                type="hidden"
                id="userId"
                value=""
            >


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
                        minlength="3"
                        maxlength="50"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="userRole">
                        Loma *
                    </label>

                    <select
                        id="userRole"
                        required
                    >

                        <option value="moderator">
                            Moderators
                        </option>

                        <option value="admin">
                            Administrators
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label
                        for="newPassword"
                        id="passwordLabel"
                    >
                        Parole *
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        minlength="8"
                        required
                    >

                </div>


                <div class="form-group">

                    <label
                        for="confirmPassword"
                        id="confirmPasswordLabel"
                    >
                        Atkārtot paroli *
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        minlength="8"
                        required
                    >

                </div>

            </div>


            <p
                id="passwordHint"
                style="
                    display:none;
                    margin-top:12px;
                    color:#758078;
                    font-size:13px;
                "
            >
                Atstājiet paroles laukus tukšus,
                ja nevēlaties mainīt paroli.
            </p>


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
                    id="saveUserButton"
                    class="primary-admin-button"
                >
                    Izveidot lietotāju
                </button>

            </div>

        </form>

    </div>

</div>


<script src="../assets/js/admin-users.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>