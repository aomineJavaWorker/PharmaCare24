const prescriptionTableBody =
    document.getElementById('prescriptionTableBody');

const prescriptionCount =
    document.getElementById('prescriptionCount');

const prescriptionMessage =
    document.getElementById('prescriptionMessage');


loadPrescriptions();


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
            result.medicines
        );


    } catch (error) {

        console.error(error);


        prescriptionMessage.textContent =
            'Neizdevās ielādēt receptes.';

        prescriptionMessage.classList.add(
            'error'
        );
    }
}

function renderPrescriptions(prescriptions) {

    prescriptionTableBody.innerHTML = '';

    prescriptionCount.textContent =
        prescriptions.length;


    if (prescriptions.length === 0) {

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

            `;


            prescriptionTableBody.appendChild(
                row
            );
        }
    );
}

function getStatusClass(status) {

    const classes = {
        'Aktīva': 'status-aktīva',
        'Izmantota': 'status-izmantota',
        'Atcelta': 'status-atcelta',
        'Beidzies termiņš': 'status-beidzies-termiņš'
    };


    return classes[status] ?? '';
}


function escapeHtml(value) {

    const element =
        document.createElement('div');


    element.textContent =
        value ?? '';


    return element.innerHTML;
}

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

const medicineSelect =
    document.getElementById('medicineId');

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


    medicines.forEach(function (medicine) {

        const option =
            document.createElement('option');


        option.value =
            medicine.id;


        if (medicine.strength) {

            option.textContent =
                `${medicine.name} (${medicine.strength})`;

        } else {

            option.textContent =
                medicine.name;
        }


        medicineSelect.appendChild(
            option
        );
    });
}

openPrescriptionModal.addEventListener(
    'click',
    function () {

        prescriptionModal.classList.add(
            'active'
        );

        document.body.style.overflow =
            'hidden';
    }
);


function closeModal() {

    prescriptionModal.classList.remove(
        'active'
    );

    document.body.style.overflow = '';

    prescriptionForm.reset();

    prescriptionFormMessage.textContent = '';

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

prescriptionForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const formData =
            new FormData(prescriptionForm);


        const data =
            Object.fromEntries(
                formData.entries()
            );


        data.quantity =
            Number(data.quantity);

        data.medicine_id =
            Number(data.medicine_id);


        try {

            const response = await fetch(
                '../api/prescriptions.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify(data)
                }
            );


            if (response.status === 401) {

                window.location.href =
                    'login.php';

                return;
            }


            const result =
                await response.json();


            if (!response.ok || !result.success) {

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

function showFormMessage(
    message,
    type
) {

    prescriptionFormMessage.textContent =
        message;

    prescriptionFormMessage.className =
        `form-message ${type}`;
}