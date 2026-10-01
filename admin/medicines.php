<?php

require_once __DIR__ . '/../includes/admin_header.php';

?>

<div class="page-header">
    <div>
        <h1>Zāļu noliktava</h1>
        <p>Aptiekā pieejamo zāļu un to atlikumu pārvaldība.</p>
    </div>

    <button type="button" id="openMedicineModal" class="primary-admin-button">
        + Pievienot zāles
    </button>
</div>

<section class="admin-panel">
    <div class="admin-panel-header">
        <div>
            <h2>Zāles</h2>
            <p>Kopā: <strong id="medicineCount">0</strong></p>
        </div>
    </div>

    <div id="medicineMessage" class="table-message">
        Ielādē zāles...
    </div>

    <div class="table-wrapper">
        <table class="admin-table actions-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nosaukums</th>
                    <th>Kategorija</th>
                    <th>Aktīvā viela</th>
                    <th>Cena</th>
                    <th>Daudzums</th>
                    <th>Recepte</th>
                    <th>Darbības</th>
                </tr>
            </thead>

            <tbody id="medicineTableBody"></tbody>
        </table>
    </div>
</section>

<div id="medicineModal" class="admin-modal">
    <div class="admin-modal-content">

        <div class="admin-modal-header">
            <div>
                <h2 id="medicineModalTitle">Pievienot zāles</h2>
                <p>Ievadiet zāļu informāciju.</p>
            </div>

            <button
                type="button"
                id="closeMedicineModal"
                class="admin-modal-close"
                aria-label="Aizvērt"
            >
                ×
            </button>
        </div>

        <form id="medicineForm" enctype="multipart/form-data">

            <input
                type="hidden"
                id="medicineId"
                name="id"
            >

            <div
                id="medicineFormMessage"
                class="form-message"
            ></div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="medicineName">Nosaukums *</label>
                    <input
                        type="text"
                        id="medicineName"
                        name="name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="medicineCategory">Kategorija *</label>
                    <select
                        id="medicineCategory"
                        name="category_id"
                        required
                    >
                        <option value="">Izvēlieties kategoriju</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="activeSubstance">Aktīvā viela *</label>
                    <input
                        type="text"
                        id="activeSubstance"
                        name="active_substance"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="manufacturer">Ražotājs *</label>
                    <input
                        type="text"
                        id="manufacturer"
                        name="manufacturer"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="dosageForm">Zāļu forma *</label>
                    <input
                        type="text"
                        id="dosageForm"
                        name="dosage_form"
                        placeholder="Tabletes"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="strength">Stiprums</label>
                    <input
                        type="text"
                        id="strength"
                        name="strength"
                        placeholder="500 mg"
                    >
                </div>

                <div class="form-group">
                    <label for="medicinePrice">Cena (€) *</label>
                    <input
                        type="number"
                        id="medicinePrice"
                        name="price"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="stockQuantity">Daudzums *</label>
                    <input
                        type="number"
                        id="stockQuantity"
                        name="stock_quantity"
                        min="0"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="apiSearchName">API meklēšanas nosaukums</label>
                    <input
                        type="text"
                        id="apiSearchName"
                        name="api_search_name"
                        placeholder="paracetamol"
                    >
                </div>

                <div class="form-group">
                    <label for="prescriptionRequired">Recepte</label>

                    <select
                        id="prescriptionRequired"
                        name="prescription_required"
                    >
                        <option value="0">Nē</option>
                        <option value="1">Jā</option>
                    </select>
                </div>


                <div class="form-group form-group-full">
                    <label for="medicineImage">Zāļu attēls</label>
                    <input
                        type="file"
                        id="medicineImage"
                        name="image"
                        accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                    >
                    <small class="form-help">JPG, PNG vai WEBP. Maksimālais faila izmērs: 2 MB.</small>

                    <div id="medicineImagePreview" class="medicine-image-preview">
                        <span>Attēls nav izvēlēts</span>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button
                    type="button"
                    id="cancelMedicineModal"
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

<script src="../assets/js/admin-medicines.js"></script>

<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>