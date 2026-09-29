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