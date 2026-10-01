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


    <button
        type="button"
        id="openPrescriptionModal"
        class="primary-admin-button"
    >
        + Pievienot recepti
    </button>

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

        <table class="admin-table actions-table">

            <thead>

                <tr>
                    <th>Nr.</th>
                    <th>Pacients</th>
                    <th>Personas kods</th>
                    <th>Ārsts</th>
                    <th>Zāles</th>
                    <th>Izrakstīta</th>
                    <th>Derīga līdz</th>
                    <th>Statuss</th>
                    <th>Darbības</th>
                </tr>

            </thead>


            <tbody id="prescriptionTableBody">

            </tbody>

        </table>

    </div>

</section>


<!-- RECEPTES MODĀLAIS LOGS -->

<div
    id="prescriptionModal"
    class="admin-modal"
>

    <div class="admin-modal-content">


        <div class="admin-modal-header">

            <div>

                <h2 id="prescriptionModalTitle">
                    Pievienot recepti
                </h2>

                <p>
                    Ievadiet receptes informāciju.
                </p>

            </div>


            <button
                type="button"
                id="closePrescriptionModal"
                class="admin-modal-close"
                aria-label="Aizvērt"
            >
                ×
            </button>

        </div>


        <form id="prescriptionForm">


            <input
                type="hidden"
                id="prescriptionId"
                name="id"
            >


            <div
                id="prescriptionFormMessage"
                class="form-message"
            ></div>


            <div class="form-grid">


                <div class="form-group">

                    <label for="prescriptionNumber">
                        Receptes numurs *
                    </label>

                    <input
                        type="text"
                        id="prescriptionNumber"
                        name="prescription_number"
                        placeholder="R-2026-0016"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="status">
                        Statuss *
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option value="Aktīva">
                            Aktīva
                        </option>

                        <option value="Izmantota">
                            Izmantota
                        </option>

                        <option value="Atcelta">
                            Atcelta
                        </option>

                        <option value="Beidzies termiņš">
                            Beidzies termiņš
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="patientName">
                        Pacients *
                    </label>

                    <input
                        type="text"
                        id="patientName"
                        name="patient_name"
                        required
                    >

                </div>
                <div class="form-group">
                    <label for="personalCode">
                        Personas kods *
                    </label>

                    <input
                        type="text"
                        id="personalCode"
                        name="personal_code"
                        placeholder="999999-00001"
                        maxlength="12"
                        pattern="[0-9]{6}-[0-9]{5}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="doctorName">
                        Ārsts *
                    </label>

                    <input
                        type="text"
                        id="doctorName"
                        name="doctor_name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="issuedAt">
                        Izrakstīšanas datums *
                    </label>

                    <input
                        type="date"
                        id="issuedAt"
                        name="issued_at"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="validUntil">
                        Derīga līdz *
                    </label>

                    <input
                        type="date"
                        id="validUntil"
                        name="valid_until"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="medicineId">
                        Zāles *
                    </label>

                    <select
                        id="medicineId"
                        name="medicine_id"
                        required
                    >

                        <option value="">
                            Izvēlieties zāles
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="quantity">
                        Daudzums *
                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="1"
                        min="1"
                        required
                    >

                </div>


            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    id="cancelPrescriptionModal"
                    class="secondary-button"
                >
                    Atcelt
                </button>


                <button
                    type="submit"
                    class="primary-admin-button"
                >
                    Saglabāt
                </button>

            </div>


        </form>

    </div>

</div>


<script src="../assets/js/admin-prescriptions.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>