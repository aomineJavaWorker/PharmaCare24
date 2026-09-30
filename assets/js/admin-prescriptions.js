const prescriptionTableBody =
    document.getElementById('prescriptionTableBody');

const prescriptionCount =
    document.getElementById('prescriptionCount');

const prescriptionMessage =
    document.getElementById('prescriptionMessage');


const openPrescriptionModal =
    document.getElementById('openPrescriptionModal');

const prescriptionModal =
    document.getElementById('prescriptionModal');

const closePrescriptionModal =
    document.getElementById('closePrescriptionModal');

const cancelPrescriptionModal =
    document.getElementById('cancelPrescriptionModal');


const prescriptionForm =
    document.getElementById('prescriptionForm');

const prescriptionFormMessage =
    document.getElementById('prescriptionFormMessage');

const prescriptionId =
    document.getElementById('prescriptionId');

const prescriptionModalTitle =
    document.getElementById('prescriptionModalTitle');

const medicineSelect =
    document.getElementById('medicineId');


loadPrescriptions();


/* =========================
   RECEPŠU IELĀDE
   ========================= */

async function loadPrescriptions() {

    try {

        const response = await fetch(
            '../api/prescriptions.php'
        );


        if (response.status === 401) {

            window.location.href =
                'login.php';

            return;
        }


        if (!response.ok) {

            throw new Error(
                'Neizdevās ielādēt receptes.'
            );
        }


        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message
            );
        }


        renderPrescriptions(
            result.data
        );


        renderMedicineOptions(
            result.medicines ?? []
        );


    } catch (error) {

        console.error(error);


        prescriptionMessage.style.display =
            'block';

        prescriptionMessage.textContent =
            'Neizdevās ielādēt receptes.';

        prescriptionMessage.classList.add(
            'error'
        );
    }
}


/* =========================
   RECEPŠU TABULA
   ========================= */

function renderPrescriptions(prescriptions) {

    prescriptionTableBody.innerHTML = '';

    prescriptionCount.textContent =
        prescriptions.length;


    if (prescriptions.length === 0) {

        prescriptionMessage.style.display =
            'block';

        prescriptionMessage.textContent =
            'Receptes nav atrastas.';

        return;
    }


    prescriptionMessage.style.display =
        'none';


    prescriptions.forEach(
        function (prescription) {

            const row =
                document.createElement('tr');


            row.innerHTML = `

                <td>
                    <strong>
                        ${escapeHtml(
                            prescription.prescription_number
                        )}
                    </strong>
                </td>


                <td>
                    ${escapeHtml(
                        prescription.patient_name
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        prescription.personal_code
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        prescription.doctor_name
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        prescription.medicines
                        ?? 'Zāles nav norādītas'
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        prescription.issued_at
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        prescription.valid_until
                    )}
                </td>


                <td>

                    <span
                        class="
                            prescription-status
                            ${getStatusClass(
                                prescription.status
                            )}
                        "
                    >
                        ${escapeHtml(
                            prescription.status
                        )}
                    </span>

                </td>


                <td>

                    <div class="table-actions">

                        <button
                            type="button"
                            class="
                                table-action-button
                                edit-prescription-button
                            "
                        >
                            Rediģēt
                        </button>


                        <button
                            type="button"
                            class="
                                table-action-button
                                delete-action-button
                                delete-prescription-button
                            "
                        >
                            Dzēst
                        </button>

                    </div>

                </td>

            `;


            prescriptionTableBody.appendChild(
                row
            );


            const editButton =
                row.querySelector(
                    '.edit-prescription-button'
                );


            editButton.addEventListener(
                'click',
                function () {

                    openEditPrescription(
                        prescription.id
                    );
                }
            );
            const deleteButton =
                row.querySelector(
                    '.delete-prescription-button'
                );


            deleteButton.addEventListener(
                'click',
                function () {

                    deletePrescription(
                        prescription.id,
                        prescription.prescription_number
                    );
                }
            );
        }
    );
}


/* =========================
   STATUSA KRĀSA
   ========================= */

function getStatusClass(status) {

    const classes = {
        'Aktīva': 'status-aktīva',
        'Izmantota': 'status-izmantota',
        'Atcelta': 'status-atcelta',
        'Beidzies termiņš':
            'status-beidzies-termiņš'
    };


    return classes[status] ?? '';
}


/* =========================
   ZĀĻU SELECT
   ========================= */

function renderMedicineOptions(medicines) {

    medicineSelect.innerHTML = '';


    const defaultOption =
        document.createElement('option');

    defaultOption.value = '';

    defaultOption.textContent =
        'Izvēlieties zāles';

    medicineSelect.appendChild(
        defaultOption
    );


    medicines.forEach(
        function (medicine) {

            const option =
                document.createElement('option');


            option.value =
                medicine.id;


            if (
                medicine.strength &&
                medicine.strength !== '-'
            ) {

                option.textContent =
                    `${medicine.name} (${medicine.strength})`;

            } else {

                option.textContent =
                    medicine.name;
            }


            medicineSelect.appendChild(
                option
            );
        }
    );
}


/* =========================
   JAUNAS RECEPTES MODAL
   ========================= */

openPrescriptionModal.addEventListener(
    'click',
    function () {

        prescriptionForm.reset();

        prescriptionId.value = '';

        prescriptionModalTitle.textContent =
            'Pievienot recepti';

        prescriptionFormMessage.textContent =
            '';

        prescriptionFormMessage.className =
            'form-message';


        prescriptionModal.classList.add(
            'active'
        );

        document.body.style.overflow =
            'hidden';
    }
);


/* =========================
   REDIĢĒŠANAS MODAL
   ========================= */

async function openEditPrescription(id) {

    try {

        const response = await fetch(
            `../api/prescriptions.php?id=${id}`
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
                ?? 'Neizdevās ielādēt recepti.'
            );

            return;
        }


        const prescription =
            result.data;


        prescriptionId.value =
            prescription.id;

        document.getElementById(
            'prescriptionNumber'
        ).value =
            prescription.prescription_number;

        document.getElementById(
            'patientName'
        ).value =
            prescription.patient_name;
        document.getElementById(
            'personalCode'
        ).value =
            prescription.personal_code;

        document.getElementById(
            'doctorName'
        ).value =
            prescription.doctor_name;

        document.getElementById(
            'issuedAt'
        ).value =
            prescription.issued_at;

        document.getElementById(
            'validUntil'
        ).value =
            prescription.valid_until;

        document.getElementById(
            'status'
        ).value =
            prescription.status;

        medicineSelect.value =
            prescription.medicine_id;

        document.getElementById(
            'quantity'
        ).value =
            prescription.quantity;


        prescriptionModalTitle.textContent =
            'Rediģēt recepti';

        prescriptionFormMessage.textContent =
            '';

        prescriptionFormMessage.className =
            'form-message';


        prescriptionModal.classList.add(
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

    prescriptionModal.classList.remove(
        'active'
    );

    document.body.style.overflow = '';

    prescriptionForm.reset();

    prescriptionId.value = '';

    prescriptionModalTitle.textContent =
        'Pievienot recepti';

    prescriptionFormMessage.textContent =
        '';

    prescriptionFormMessage.className =
        'form-message';
}


closePrescriptionModal.addEventListener(
    'click',
    closeModal
);


cancelPrescriptionModal.addEventListener(
    'click',
    closeModal
);


prescriptionModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target === prescriptionModal
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
            prescriptionModal.classList.contains(
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

prescriptionForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const formData =
            new FormData(
                prescriptionForm
            );


        const data =
            Object.fromEntries(
                formData.entries()
            );


        data.quantity =
            Number(data.quantity);

        data.medicine_id =
            Number(data.medicine_id);


        const isEditing =
            prescriptionId.value !== '';


        if (isEditing) {

            data.id =
                Number(
                    prescriptionId.value
                );
        }


        try {

            const response = await fetch(
                '../api/prescriptions.php',
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
                    ?? 'Neizdevās saglabāt recepti.',
                    'error'
                );

                return;
            }


            showFormMessage(
                result.message,
                'success'
            );


            await loadPrescriptions();


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
   RECEPTES DZĒŠANA
   ========================= */

async function deletePrescription(
    id,
    prescriptionNumber
) {

    const confirmed = confirm(
        `Vai tiešām vēlaties dzēst recepti ${prescriptionNumber}?`
    );


    if (!confirmed) {
        return;
    }


    try {

        const response = await fetch(
            '../api/prescriptions.php',
            {
                method: 'DELETE',

                headers: {
                    'Content-Type':
                        'application/json'
                },

                body: JSON.stringify({
                    id: id
                })
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
                ?? 'Neizdevās dzēst recepti.'
            );

            return;
        }


        await loadPrescriptions();


    } catch (error) {

        console.error(error);

        alert(
            'Neizdevās sazināties ar serveri.'
        );
    }
}

/* =========================
   PAZIŅOJUMI
   ========================= */

function showFormMessage(
    message,
    type
) {

    prescriptionFormMessage.textContent =
        message;

    prescriptionFormMessage.className =
        `form-message ${type}`;
}


/* =========================
   XSS AIZSARDZĪBA
   ========================= */

function escapeHtml(value) {

    const element =
        document.createElement('div');


    element.textContent =
        value ?? '';


    return element.innerHTML;
}