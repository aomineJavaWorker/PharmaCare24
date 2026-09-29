const medicineTableBody = document.getElementById('medicineTableBody');
const medicineCount = document.getElementById('medicineCount');
const medicineMessage = document.getElementById('medicineMessage');


loadMedicines();


async function loadMedicines() {

    try {

        const response = await fetch('../api/medicines.php');

        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message);
        }

        renderMedicines(result.data);

    } catch (error) {

        console.error(error);

        medicineMessage.style.display = 'block';
        medicineMessage.textContent = 'Neizdevās ielādēt zāles.';
        medicineMessage.classList.add('error');
    }
}


function renderMedicines(medicines) {

    medicineTableBody.innerHTML = '';
    medicineCount.textContent = medicines.length;


    if (medicines.length === 0) {
        medicineMessage.style.display = 'block';
        medicineMessage.textContent = 'Zāles nav atrastas.';
        return;
    }


    medicineMessage.style.display = 'none';


    medicines.forEach(function (medicine) {

        const row = document.createElement('tr');

        const stockClass =
            Number(medicine.stock_quantity) <= 10
                ? 'stock-low'
                : '';


        const prescription = Number(medicine.prescription_required)
            ? '<span class="table-badge prescription-yes">Jā</span>'
            : '<span class="table-badge prescription-no">Nē</span>';


        row.innerHTML = `
            <td>#${medicine.id}</td>

            <td>
                <strong>${escapeHtml(medicine.name)}</strong>
            </td>

            <td>
                ${escapeHtml(medicine.category_name ?? 'Bez kategorijas')}
            </td>

            <td>
                ${escapeHtml(medicine.active_substance ?? '-')}
            </td>

            <td>
                ${Number(medicine.price).toFixed(2).replace('.', ',')} €
            </td>

            <td>
                <span class="stock-value ${stockClass}">
                    ${medicine.stock_quantity}
                </span>
            </td>

            <td>
                ${prescription}
            </td>
        `;

        medicineTableBody.appendChild(row);
    });
}


function escapeHtml(value) {

    const element = document.createElement('div');
    element.textContent = value ?? '';

    return element.innerHTML;
}