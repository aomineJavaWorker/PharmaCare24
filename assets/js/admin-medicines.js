const medicineTableBody =
    document.getElementById('medicineTableBody');

const medicineCount =
    document.getElementById('medicineCount');

const medicineMessage =
    document.getElementById('medicineMessage');


const openMedicineModal =
    document.getElementById('openMedicineModal');

const medicineModal =
    document.getElementById('medicineModal');

const closeMedicineModal =
    document.getElementById('closeMedicineModal');

const cancelMedicineModal =
    document.getElementById('cancelMedicineModal');


const medicineForm =
    document.getElementById('medicineForm');

const medicineFormMessage =
    document.getElementById('medicineFormMessage');

const medicineModalTitle =
    document.getElementById('medicineModalTitle');

const medicineId =
    document.getElementById('medicineId');


const medicineName =
    document.getElementById('medicineName');

const categorySelect =
    document.getElementById('medicineCategory');

const activeSubstance =
    document.getElementById('activeSubstance');

const manufacturer =
    document.getElementById('manufacturer');

const dosageForm =
    document.getElementById('dosageForm');

const strength =
    document.getElementById('strength');

const medicinePrice =
    document.getElementById('medicinePrice');

const stockQuantity =
    document.getElementById('stockQuantity');

const apiSearchName =
    document.getElementById('apiSearchName');

const prescriptionRequired =
    document.getElementById('prescriptionRequired');

const medicineImage =
    document.getElementById('medicineImage');

const medicineImagePreview =
    document.getElementById('medicineImagePreview');

let selectedImagePreviewUrl = null;


loadMedicines();


/* =========================
   ZĀĻU IELĀDE
   ========================= */

async function loadMedicines() {

    try {

        const response =
            await fetch('../api/medicines.php');


        if (response.status === 401) {

            window.location.href =
                'login.php';

            return;
        }


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            throw new Error(
                result.message
            );
        }


        renderMedicines(
            result.data
        );


        renderCategories(
            result.categories ?? []
        );


    } catch (error) {

        console.error(error);


        medicineMessage.style.display =
            'block';

        medicineMessage.textContent =
            'Neizdevās ielādēt zāles.';

        medicineMessage.classList.add(
            'error'
        );
    }
}


/* =========================
   ZĀĻU TABULA
   ========================= */

function renderMedicines(medicines) {

    medicineTableBody.innerHTML = '';

    medicineCount.textContent =
        medicines.length;


    if (medicines.length === 0) {

        medicineMessage.style.display =
            'block';

        medicineMessage.textContent =
            'Zāles nav atrastas.';

        return;
    }


    medicineMessage.style.display =
        'none';


    medicines.forEach(
        function (medicine) {

            const row =
                document.createElement('tr');


            const stockClass =
                Number(
                    medicine.stock_quantity
                ) <= 10
                    ? 'stock-low'
                    : '';


            const prescription =
                Number(
                    medicine.prescription_required
                )
                    ? '<span class="table-badge prescription-yes">Jā</span>'
                    : '<span class="table-badge prescription-no">Nē</span>';


            row.innerHTML = `
                <td>
                    #${medicine.id}
                </td>

                <td>
                    <strong>
                        ${escapeHtml(
                            medicine.name
                        )}
                    </strong>
                </td>

                <td>
                    ${escapeHtml(
                        medicine.category_name
                        ?? 'Bez kategorijas'
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        medicine.active_substance
                        ?? '-'
                    )}
                </td>

                <td>
                    ${Number(
                        medicine.price
                    )
                        .toFixed(2)
                        .replace('.', ',')} €
                </td>

                <td>
                    <span
                        class="stock-value ${stockClass}"
                    >
                        ${medicine.stock_quantity}
                    </span>
                </td>

                <td>
                    ${prescription}
                </td>

                <td>
                    <div class="table-actions">
                        <button
                            type="button"
                            class="
                                table-action-button
                                edit-medicine-button
                            "
                        >
                            Rediģēt
                        </button>

                        <button
                            type="button"
                            class="
                                table-action-button
                                delete-action-button
                                delete-medicine-button
                            "
                        >
                            Dzēst
                        </button>

                    </div>
                </td>
            `;


            medicineTableBody.appendChild(
                row
            );


            const editButton =
                row.querySelector(
                    '.edit-medicine-button'
                );


            editButton.addEventListener(
                'click',
                function () {

                    openEditMedicine(
                        medicine.id
                    );
                }
            );

            const deleteButton =
                row.querySelector(
                    '.delete-medicine-button'
                );


            deleteButton.addEventListener(
                'click',
                function () {

                    deleteMedicine(
                        medicine.id,
                        medicine.name
                    );
                }
            );
        }
    );
}


/* =========================
   KATEGORIJAS
   ========================= */

function renderCategories(categories) {

    categorySelect.innerHTML =
        '<option value="">Izvēlieties kategoriju</option>';


    categories.forEach(
        function (category) {

            const option =
                document.createElement('option');


            option.value =
                category.id;

            option.textContent =
                category.name;


            categorySelect.appendChild(
                option
            );
        }
    );
}


/* =========================
   JAUNU ZĀĻU MODAL
   ========================= */

openMedicineModal.addEventListener(
    'click',
    function () {

        medicineForm.reset();

        medicineId.value = '';


        medicineModalTitle.textContent =
            'Pievienot zāles';


        clearFormMessage();
        
        resetImagePreview();

        medicineModal.classList.add(
            'active'
        );
        
        document.body.style.overflow =
            'hidden';
    }
);


/* =========================
   REDIĢĒŠANAS MODAL
   ========================= */

async function openEditMedicine(id) {

    try {

        const response = await fetch(
            `../api/medicines.php?id=${id}`
        );


        if (response.status === 401) {

            window.location.href =
                'login.php';

            return;
        }


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message
                ?? 'Neizdevās ielādēt zāles.'
            );

            return;
        }


        const medicine =
            result.data;


        medicineId.value =
            medicine.id;


        medicineName.value =
            medicine.name ?? '';


        categorySelect.value =
            medicine.category_id ?? '';


        activeSubstance.value =
            medicine.active_substance ?? '';


        manufacturer.value =
            medicine.manufacturer ?? '';


        dosageForm.value =
            medicine.dosage_form ?? '';


        strength.value =
            medicine.strength ?? '';


        medicinePrice.value =
            medicine.price ?? '';


        stockQuantity.value =
            medicine.stock_quantity ?? 0;


        apiSearchName.value =
            medicine.api_search_name ?? '';


        prescriptionRequired.value =
            Number(
                medicine.prescription_required
            )
                ? '1'
                : '0';

        showStoredImagePreview(
            medicine.image ?? ''
        );


        medicineModalTitle.textContent =
            'Rediģēt zāles';


        clearFormMessage();


        medicineModal.classList.add(
            'active'
        );


        document.body.style.overflow =
            'hidden';


    } catch (error) {

        console.error(error);


        alert(
            'Neizdevās sazināties ar serveri.'
        );
    }
}


/* =========================
   MODAL AIZVĒRŠANA
   ========================= */

function closeModal() {

    medicineModal.classList.remove(
        'active'
    );


    document.body.style.overflow = '';


    medicineForm.reset();


    medicineId.value = '';


    medicineModalTitle.textContent =
        'Pievienot zāles';

    resetImagePreview();

    clearFormMessage();
}


closeMedicineModal.addEventListener(
    'click',
    closeModal
);


cancelMedicineModal.addEventListener(
    'click',
    closeModal
);


medicineModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target ===
            medicineModal
        ) {

            closeModal();
        }
    }
);


document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key === 'Escape' &&
            medicineModal.classList.contains(
                'active'
            )
        ) {

            closeModal();
        }
    }
);


/* =========================
   PIEVIENOŠANA / REDIĢĒŠANA
   ========================= */

medicineForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const formData =
            new FormData(
                medicineForm
            );


        const data =
            Object.fromEntries(
                formData.entries()
            );

        delete data.image;


        data.category_id =
            Number(
                data.category_id
            );


        data.price =
            Number(
                data.price
            );


        data.stock_quantity =
            Number(
                data.stock_quantity
            );


        data.prescription_required =
            Number(
                data.prescription_required
            );


        const isEditing =
            medicineId.value !== '';


        if (isEditing) {

            data.id =
                Number(
                    medicineId.value
                );
        }


        try {

            const response = await fetch(
                '../api/medicines.php',
                {
                    method: isEditing
                        ? 'PUT'
                        : 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body:
                        JSON.stringify(data)
                }
            );


            if (response.status === 401) {

                window.location.href =
                    'login.php';

                return;
            }


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                showFormMessage(
                    result.message
                    ?? 'Neizdevās saglabāt zāles.',
                    'error'
                );

                return;
            }

            const savedMedicineId =
    Number(
        result.id
        ?? medicineId.value
    );


            if (medicineImage.files.length > 0) {

                const uploadResult =
                    await uploadMedicineImage(
                        savedMedicineId
                    );


                if (!uploadResult.success) {

                    showFormMessage(
                        'Zāļu dati saglabāti, bet attēlu neizdevās augšupielādēt: ' +
                        uploadResult.message,
                        'error'
                    );

                    await loadMedicines();

                    return;
                }
            }

            showFormMessage(
                result.message,
                'success'
            );


            await loadMedicines();


            setTimeout(
                closeModal,
                700
            );


        } catch (error) {

            console.error(error);


            showFormMessage(
                'Neizdevās sazināties ar serveri.',
                'error'
            );
        }
    }
);

/* =========================
   ATTĒLA PRIEKŠSKATĪJUMS
   ========================= */

medicineImage.addEventListener(
    'change',
    function () {

        const file =
            medicineImage.files[0];


        if (!file) {

            resetImagePreview();

            return;
        }


        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (!allowedTypes.includes(file.type)) {

            medicineImage.value = '';

            showFormMessage(
                'Atļauti tikai JPG, PNG un WEBP attēli.',
                'error'
            );

            resetImagePreview();

            return;
        }


        if (file.size > 2 * 1024 * 1024) {

            medicineImage.value = '';

            showFormMessage(
                'Attēls ir pārāk liels. Maksimālais izmērs ir 2 MB.',
                'error'
            );

            resetImagePreview();

            return;
        }


        clearFormMessage();


        if (selectedImagePreviewUrl) {

            URL.revokeObjectURL(
                selectedImagePreviewUrl
            );
        }


        selectedImagePreviewUrl =
            URL.createObjectURL(file);


        medicineImagePreview.innerHTML = `
            <img
                src="${selectedImagePreviewUrl}"
                alt="Zāļu attēla priekšskatījums"
            >
        `;
    }
);


function showStoredImagePreview(imagePath) {

    resetImagePreview();


    if (!imagePath) {

        return;
    }


    const image =
        document.createElement('img');


    image.src =
        '../' + imagePath;

    image.alt =
        'Esošais zāļu attēls';


    medicineImagePreview.innerHTML = '';

    medicineImagePreview.appendChild(
        image
    );
}


function resetImagePreview() {

    if (selectedImagePreviewUrl) {

        URL.revokeObjectURL(
            selectedImagePreviewUrl
        );

        selectedImagePreviewUrl = null;
    }


    medicineImagePreview.innerHTML =
        '<span>Attēls nav izvēlēts</span>';
}


/* =========================
   ATTĒLA AUGŠUPIELĀDE
   ========================= */

async function uploadMedicineImage(id) {

    if (!id) {

        return {
            success: false,
            message: 'Nav norādīts zāļu ID.'
        };
    }


    const file =
        medicineImage.files[0];


    if (!file) {

        return {
            success: true
        };
    }


    const uploadData =
        new FormData();


    uploadData.append(
        'medicine_id',
        id
    );


    uploadData.append(
        'image',
        file
    );


    try {

        const response =
            await fetch(
                '../api/medicine-image.php',
                {
                    method: 'POST',
                    body: uploadData
                }
            );


        if (response.status === 401) {

            window.location.href =
                'login.php';


            return {
                success: false,
                message: 'Nav autorizācijas.'
            };
        }


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            return {
                success: false,
                message:
                    result.message
                    ?? 'Neizdevās augšupielādēt attēlu.'
            };
        }


        return result;


    } catch (error) {

        console.error(error);


        return {
            success: false,
            message: 'Neizdevās sazināties ar serveri.'
        };
    }
}


/* =========================
   FORMAS PAZIŅOJUMI
   ========================= */

function showFormMessage(
    message,
    type
) {

    medicineFormMessage.textContent =
        message;


    medicineFormMessage.className =
        `form-message ${type}`;
}

/* =========================
   ZĀĻU DZĒŠANA
   ========================= */

async function deleteMedicine(id, name) {

    const confirmed =
        confirm(
            `Vai tiešām vēlaties dzēst zāles "${name}"?`
        );


    if (!confirmed) {
        return;
    }


    try {

        const response = await fetch(
            '../api/medicines.php',
            {
                method: 'DELETE',

                headers: {
                    'Content-Type':
                        'application/json'
                },

                body: JSON.stringify({id: id})
            }
        );


        if (response.status === 401) {

            window.location.href =
                'login.php';

            return;
        }


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message
                ?? 'Neizdevās dzēst zāles.'
            );

            return;
        }
        await loadMedicines();

    } catch (error) {

        console.error(error);


        alert(
            'Neizdevās sazināties ar serveri.'
        );
    }
}

function clearFormMessage() {

    medicineFormMessage.textContent =
        '';


    medicineFormMessage.className =
        'form-message';
}


/* =========================
   HTML DROŠĪBA
   ========================= */

function escapeHtml(value) {

    const element =
        document.createElement('div');


    element.textContent =
        value ?? '';


    return element.innerHTML;
}