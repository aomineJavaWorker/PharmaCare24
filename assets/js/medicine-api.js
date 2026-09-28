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

        const activeSubstance =
            button.dataset.activeSubstance;

        const apiSearchName =
            button.dataset.apiSearchName;


        openModal();


        if (!apiSearchName) {

            showError(
                'Šīm zālēm nav norādīts API meklēšanas nosaukums.'
            );

            return;
        }


        showLoading(
            `Meklē informāciju par ${medicineName}...`
        );


        try {

            const encodedName =
                encodeURIComponent(apiSearchName);


            const response = await fetch(
                `https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/${encodedName}/property/Title,MolecularFormula,MolecularWeight,IUPACName/JSON`
            );


            if (!response.ok) {

                if (response.status === 404) {

                    showError(
                        'Papildu informācija par šīm zālēm PubChem datubāzē netika atrasta.'
                    );

                    return;
                }


                throw new Error(
                    'PubChem API pieprasījuma kļūda.'
                );
            }


            const data =
                await response.json();


            const compound =
                data.PropertyTable?.Properties?.[0];


            if (!compound) {

                showError(
                    'PubChem neatgrieza informāciju par šo vielu.'
                );

                return;
            }


            showMedicineInformation(
                compound,
                medicineName,
                activeSubstance
            );

        } catch (error) {

            console.error(error);


            showError(
                'Neizdevās iegūt informāciju no PubChem API. Lūdzu, mēģiniet vēlreiz vēlāk.'
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
    compound,
    medicineName,
    activeSubstance
) {

    const pubChemName =
        compound.Title
        ?? 'Nav norādīts';


    const formula =
        compound.MolecularFormula
        ?? 'Nav norādīts';


    const molecularWeight =
        compound.MolecularWeight
        ?? 'Nav norādīts';


    const iupacName =
        compound.IUPACName
        ?? 'Nav norādīts';


    const cid =
        compound.CID
        ?? 'Nav norādīts';


    medicineModalBody.innerHTML = `

        <h2 class="modal-medicine-title">
            ${escapeHtml(medicineName)}
        </h2>


        <p class="modal-medicine-subtitle">
            Papildu informācija no PubChem
        </p>


        <div class="modal-info-grid">

            <div class="modal-info-item">

                <span>
                    Aktīvā viela
                </span>

                <strong>
                    ${escapeHtml(activeSubstance)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    PubChem nosaukums
                </span>

                <strong>
                    ${escapeHtml(pubChemName)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    Molekulārā formula
                </span>

                <strong>
                    ${escapeHtml(formula)}
                </strong>

            </div>


            <div class="modal-info-item">

                <span>
                    Molekulmasa
                </span>

                <strong>
                    ${escapeHtml(String(molecularWeight))} g/mol
                </strong>

            </div>

        </div>


        <div class="modal-description">

            <h4>
                IUPAC nosaukums
            </h4>

            <p>
                ${escapeHtml(iupacName)}
            </p>

        </div>


        <div class="modal-description">

            <h4>
                PubChem CID
            </h4>

            <p>
                ${escapeHtml(String(cid))}
            </p>

        </div>

    `;
}

function escapeHtml(value) {

    const element =
        document.createElement('div');

    element.textContent =
        value ?? '';

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