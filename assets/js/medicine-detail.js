(function () {
    'use strict';
    const button = document.getElementById('detailAddCart');
    if (!button) return;

    const quantityInput = document.getElementById('detailQty');
    const feedback = document.getElementById('detailFeedback');
    const storageKey = 'aptieka_cart';

    button.addEventListener('click', function () {
        const id = Number(button.dataset.id);
        const name = button.dataset.name;
        const price = Number(button.dataset.price);
        const stock = Number(button.dataset.stock);
        const prescriptionRequired = button.dataset.prescription === '1';
        const quantity = Number(quantityInput.value);

        if (!Number.isInteger(quantity) || quantity < 1 || quantity > stock) {
            feedback.textContent = 'Ievadiet derīgu daudzumu, kas nepārsniedz atlikumu.';
            feedback.dataset.status = 'error';
            return;
        }

        let cart;
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || '[]');
            cart = Array.isArray(saved) ? saved : [];
        } catch (error) {
            cart = [];
        }

        const existing = cart.find(function (item) { return Number(item.id) === id; });
        if (existing) {
            if (Number(existing.quantity) + quantity > stock) {
                feedback.textContent = 'Grozā jau ir šīs zāles. Kopējais daudzums pārsniedz atlikumu.';
                feedback.dataset.status = 'error';
                return;
            }
            existing.quantity = Number(existing.quantity) + quantity;
        } else {
            cart.push({ id, name, price, stock, prescriptionRequired, quantity });
        }

        try {
            localStorage.setItem(storageKey, JSON.stringify(cart));
        } catch (error) {
            feedback.textContent = 'Neizdevās saglabāt grozu pārlūkā.';
            feedback.dataset.status = 'error';
            return;
        }

        feedback.textContent = 'Produkts pievienots grozam. Atveriet katalogu, lai apskatītu grozu.';
        feedback.dataset.status = 'success';
    });
})();
