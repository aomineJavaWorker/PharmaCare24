const infoButtons =
    document.querySelectorAll('.medicine-info-button');

const medicineModal =
    document.getElementById('medicineModal');

const medicineModalBody =
    document.getElementById('medicineModalBody');

const medicineModalClose =
    document.getElementById('medicineModalClose');


infoButtons.forEach(function (button) {

    button.addEventListener('click', async function () {

        const medicineName =
            button.dataset.medicineName;


        openModal();


        showLoading(
            `Meklē informāciju par ${medicineName}...`
        );


        const query = encodeURIComponent(
            `openfda.generic_name:"${medicineName}" OR openfda.brand_name:"${medicineName}"`
        );


        try {

            const response = await fetch(
                `https://api.fda.gov/drug/label.json?search=${query}&limit=1`
            );


            if (!response.ok) {

                if (response.status === 404) {

                    showError(
                        'Papildu informācija par šīm zālēm ārējā datu avotā netika atrasta.'
                    );

                    return;
                }


                throw new Error(
                    'API pieprasījuma kļūda.'
                );
            }


            const data =
                await response.json();


            const medicine =
                data.results[0];


            showMedicineInformation(
                medicine,
                medicineName
            );

        } catch (error) {

            console.error(error);


            showError(
                'Neizdevās iegūt informāciju no ārējā API. Lūdzu, mēģiniet vēlreiz vēlāk.'
            );
        }

    });

});

function openModal() {

    medicineModal.classList.add('active');

    document.body.style.overflow = 'hidden';
}


function closeModal() {

    medicineModal.classList.remove('active');

    document.body.style.overflow = '';
}


function showLoading(message) {

    medicineModalBody.innerHTML = `
        <div class="modal-loading">
            ${escapeHtml(message)}
        </div>
    `;
}


function showError(message) {

    medicineModalBody.innerHTML = `
        <div class="modal-error">
            ${escapeHtml(message)}
        </div>
    `;
}


function showMedicineInformation(
    medicine,
    searchedName
) {

    const genericName =
        medicine.openfda?.generic_name?.[0]
        ?? searchedName;


    const brandName =
        medicine.openfda?.brand_name?.[0]
        ?? 'Nav norādīts';


    const manufacturer =
        medicine.openfda?.manufacturer_name?.[0]
        ?? 'Nav norādīts';


    const route =
        medicine.openfda?.route?.[0]
        ?? 'Nav norādīts';


    const purpose =
        medicine.purpose?.[0]
        ?? 'Informācija nav pieejama';


    medicineModalBody.innerHTML = `

        <h2 class="modal-medicine-title">
            ${escapeHtml(genericName)}
        </h2>


        <p class="modal-medicine-subtitle">
            Informācija no openFDA
        </p>


        <div class="modal-info-grid">

            <div class="modal-info-item">

                <span>
                    Zīmola nosaukums
                </span>

                <strong>
                    ${escapeHtml(brandName)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    Ražotājs
                </span>

                <strong>
                    ${escapeHtml(manufacturer)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    Aktīvā viela
                </span>

                <strong>
                    ${escapeHtml(genericName)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    Lietošanas veids
                </span>

                <strong>
                    ${escapeHtml(route)}
                </strong>

            </div>

        </div>


        <div class="modal-description">

            <h4>
                Lietošanas mērķis
            </h4>

            <p>
                ${escapeHtml(
                    shortenText(purpose, 700)
                )}
            </p>

        </div>

    `;
}

function shortenText(
    text,
    maxLength
) {

    if (text.length <= maxLength) {
        return text;
    }


    return text.substring(
        0,
        maxLength
    ) + '...';
}


function escapeHtml(value) {

    const element =
        document.createElement('div');


    element.textContent =
        value;


    return element.innerHTML;
}

medicineModalClose.addEventListener(
    'click',
    closeModal
);


medicineModal.addEventListener(
    'click',
    function (event) {

        if (event.target === medicineModal) {

            closeModal();
        }

    }
);


document.addEventListener(
    'keydown',
    function (event) {

        if (event.key === 'Escape') {

            closeModal();
        }

    }
);