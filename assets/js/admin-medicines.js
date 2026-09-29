const medicineTableBody = document.getElementById('medicineTableBody');
const medicineCount = document.getElementById('medicineCount');
const medicineMessage = document.getElementById('medicineMessage');

const openMedicineModal = document.getElementById('openMedicineModal');
const medicineModal = document.getElementById('medicineModal');
const closeMedicineModal = document.getElementById('closeMedicineModal');
const cancelMedicineModal = document.getElementById('cancelMedicineModal');

const medicineForm = document.getElementById('medicineForm');
const medicineFormMessage = document.getElementById('medicineFormMessage');
const categorySelect = document.getElementById('medicineCategory');


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
        renderCategories(result.categories ?? []);

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
            <td><strong>${escapeHtml(medicine.name)}</strong></td>
            <td>${escapeHtml(medicine.category_name ?? 'Bez kategorijas')}</td>
            <td>${escapeHtml(medicine.active_substance ?? '-')}</td>
            <td>${Number(medicine.price).toFixed(2).replace('.', ',')} €</td>
            <td>
                <span class="stock-value ${stockClass}">
                    ${medicine.stock_quantity}
                </span>
            </td>
            <td>${prescription}</td>
        `;

        medicineTableBody.appendChild(row);
    });
}


function renderCategories(categories) {
    categorySelect.innerHTML =
        '<option value="">Izvēlieties kategoriju</option>';

    categories.forEach(function (category) {
        const option = document.createElement('option');

        option.value = category.id;
        option.textContent = category.name;

        categorySelect.appendChild(option);
    });
}


openMedicineModal.addEventListener('click', function () {
    medicineForm.reset();
    clearFormMessage();

    medicineModal.classList.add('active');
    document.body.style.overflow = 'hidden';
});


function closeModal() {
    medicineModal.classList.remove('active');
    document.body.style.overflow = '';

    medicineForm.reset();
    clearFormMessage();
}


closeMedicineModal.addEventListener('click', closeModal);
cancelMedicineModal.addEventListener('click', closeModal);


medicineModal.addEventListener('click', function (event) {
    if (event.target === medicineModal) {
        closeModal();
    }
});


document.addEventListener('keydown', function (event) {
    if (
        event.key === 'Escape' &&
        medicineModal.classList.contains('active')
    ) {
        closeModal();
    }
});


medicineForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(medicineForm);
    const data = Object.fromEntries(formData.entries());

    data.category_id = Number(data.category_id);
    data.price = Number(data.price);
    data.stock_quantity = Number(data.stock_quantity);
    data.prescription_required = Number(data.prescription_required);

    try {
        const response = await fetch('../api/medicines.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage(
                result.message ?? 'Neizdevās saglabāt zāles.',
                'error'
            );
            return;
        }

        showFormMessage(result.message, 'success');

        await loadMedicines();

        setTimeout(closeModal, 700);

    } catch (error) {
        console.error(error);

        showFormMessage(
            'Neizdevās sazināties ar serveri.',
            'error'
        );
    }
});


function showFormMessage(message, type) {
    medicineFormMessage.textContent = message;
    medicineFormMessage.className = `form-message ${type}`;
}


function clearFormMessage() {
    medicineFormMessage.textContent = '';
    medicineFormMessage.className = 'form-message';
}


function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}