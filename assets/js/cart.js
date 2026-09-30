const cartButton =
    document.getElementById('cartButton');

const cartCount =
    document.getElementById('cartCount');

const cartModal =
    document.getElementById('cartModal');

const cartCloseButton =
    document.getElementById('cartCloseButton');

const cartItems =
    document.getElementById('cartItems');

const cartTotal =
    document.getElementById('cartTotal');

const cartMessage =
    document.getElementById('cartMessage');

const cartPrescriptionWarning =
    document.getElementById(
        'cartPrescriptionWarning'
    );

const continueCheckoutButton =
    document.getElementById(
        'continueCheckoutButton'
    );

const buyButtons =
    document.querySelectorAll(
        '.medicine-buy-button'
    );


let cart = loadCart();


renderCart();


/* =========================
   PIRKT
   ========================= */

buyButtons.forEach(function (button) {

    button.addEventListener(
        'click',
        function () {

            const medicine = {
                id: Number(
                    button.dataset.medicineId
                ),

                name:
                    button.dataset.medicineName,

                price: Number(
                    button.dataset.medicinePrice
                ),

                stock: Number(
                    button.dataset.medicineStock
                ),

                prescriptionRequired:
                    Number(
                        button.dataset
                            .prescriptionRequired
                    ) === 1,

                quantity: 1
            };


            addToCart(medicine);
        }
    );
});


function addToCart(medicine) {

    const existingItem =
        cart.find(
            item =>
                item.id === medicine.id
        );


    if (existingItem) {

        if (
            existingItem.quantity >=
            medicine.stock
        ) {

            showCartMessage(
                'Noliktavā nav vairāk šo zāļu.',
                'error'
            );

            openCart();

            return;
        }


        existingItem.quantity++;

    } else {

        cart.push(medicine);
    }


    saveCart();

    renderCart();

    showCartMessage(
        `"${medicine.name}" pievienots grozam.`,
        'success'
    );


    openCart();
}


/* =========================
   GROZA ATTĒLOŠANA
   ========================= */

function renderCart() {

    cartItems.innerHTML = '';


    if (cart.length === 0) {

        cartItems.innerHTML = `
            <p class="cart-empty">
                Grozs ir tukšs.
            </p>
        `;


        cartTotal.textContent =
            '0,00 €';


        cartCount.textContent =
            '0';


        cartPrescriptionWarning.style.display =
            'none';


        continueCheckoutButton.disabled =
            true;


        return;
    }


    let total = 0;

    let totalQuantity = 0;

    let containsPrescriptionMedicine =
        false;


    cart.forEach(function (item) {

        total +=
            item.price * item.quantity;


        totalQuantity +=
            item.quantity;


        if (
            item.prescriptionRequired
        ) {

            containsPrescriptionMedicine =
                true;
        }


        const itemElement =
            document.createElement('div');


        itemElement.className =
            'cart-item';


        itemElement.innerHTML = `

            <div class="cart-item-info">

                <strong>
                    ${escapeCartHtml(
                        item.name
                    )}
                </strong>


                ${
                    item.prescriptionRequired

                        ? `
                            <span
                                class="
                                    cart-prescription-label
                                "
                            >
                                Nepieciešama recepte
                            </span>
                        `

                        : `
                            <span
                                class="
                                    cart-free-label
                                "
                            >
                                Bez receptes
                            </span>
                        `
                }


                <small>
                    ${formatPrice(
                        item.price
                    )} / gab.
                </small>

            </div>


            <div class="cart-item-controls">

                <button
                    type="button"
                    class="cart-quantity-button"
                    data-action="decrease"
                    data-id="${item.id}"
                >
                    −
                </button>


                <span class="cart-item-quantity">
                    ${item.quantity}
                </span>


                <button
                    type="button"
                    class="cart-quantity-button"
                    data-action="increase"
                    data-id="${item.id}"
                >
                    +
                </button>

            </div>


            <div class="cart-item-price">

                <strong>
                    ${formatPrice(
                        item.price *
                        item.quantity
                    )}
                </strong>


                <button
                    type="button"
                    class="cart-remove-button"
                    data-action="remove"
                    data-id="${item.id}"
                >
                    Dzēst
                </button>

            </div>

        `;


        cartItems.appendChild(
            itemElement
        );
    });


    cartTotal.textContent =
        formatPrice(total);


    cartCount.textContent =
        totalQuantity;


    cartPrescriptionWarning.style.display =
        containsPrescriptionMedicine
            ? 'block'
            : 'none';


    continueCheckoutButton.disabled =
        false;


    addCartItemEvents();
}


/* =========================
   + / - / DZĒST
   ========================= */

function addCartItemEvents() {

    const buttons =
        cartItems.querySelectorAll(
            '[data-action]'
        );


    buttons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function () {

                    const id =
                        Number(
                            button.dataset.id
                        );

                    const action =
                        button.dataset.action;


                    if (
                        action ===
                        'increase'
                    ) {

                        increaseQuantity(id);
                    }


                    if (
                        action ===
                        'decrease'
                    ) {

                        decreaseQuantity(id);
                    }


                    if (
                        action ===
                        'remove'
                    ) {

                        removeFromCart(id);
                    }

                }
            );
        }
    );
}


function increaseQuantity(id) {

    const item =
        cart.find(
            item => item.id === id
        );


    if (!item) {
        return;
    }


    if (
        item.quantity >=
        item.stock
    ) {

        showCartMessage(
            'Sasniegts maksimālais noliktavā pieejamais daudzums.',
            'error'
        );

        return;
    }


    item.quantity++;


    saveCart();

    renderCart();
}


function decreaseQuantity(id) {

    const item =
        cart.find(
            item => item.id === id
        );


    if (!item) {
        return;
    }


    if (item.quantity <= 1) {

        removeFromCart(id);

        return;
    }


    item.quantity--;


    saveCart();

    renderCart();
}


function removeFromCart(id) {

    cart =
        cart.filter(
            item => item.id !== id
        );


    saveCart();

    renderCart();
}


/* =========================
   MODAL
   ========================= */

function openCart() {

    cartModal.classList.add(
        'active'
    );


    document.body.style.overflow =
        'hidden';
}


function closeCart() {

    cartModal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';
}


cartButton.addEventListener(
    'click',
    openCart
);


cartCloseButton.addEventListener(
    'click',
    closeCart
);


cartModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target ===
            cartModal
        ) {

            closeCart();
        }
    }
);


document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key ===
                'Escape' &&
            cartModal.classList.contains(
                'active'
            )
        ) {

            closeCart();
        }
    }
);


/* =========================
   TURPINĀT
   ========================= */

continueCheckoutButton.addEventListener(
    'click',
    function () {

        if (cart.length === 0) {
            return;
        }


        const hasPrescriptionMedicine =
            cart.some(
                item =>
                    item
                        .prescriptionRequired
            );


        if (hasPrescriptionMedicine) {

            showCartMessage(
                'Nākamajā etapā šeit pievienosim receptes pārbaudi.',
                'info'
            );

            return;
        }


        showCartMessage(
            'Grozs ir gatavs apmaksai. Stripe pievienosim nākamajos etapos.',
            'info'
        );
    }
);


/* =========================
   LOCAL STORAGE
   ========================= */

function saveCart() {

    localStorage.setItem(
        'aptieka_cart',
        JSON.stringify(cart)
    );
}


function loadCart() {

    try {

        const savedCart =
            localStorage.getItem(
                'aptieka_cart'
            );


        if (!savedCart) {
            return [];
        }


        const parsed =
            JSON.parse(savedCart);


        if (!Array.isArray(parsed)) {
            return [];
        }


        return parsed;


    } catch (error) {

        console.error(error);

        return [];
    }
}


/* =========================
   PAZIŅOJUMI
   ========================= */

function showCartMessage(
    message,
    type
) {

    cartMessage.textContent =
        message;


    cartMessage.className =
        `cart-message ${type}`;


    cartMessage.style.display =
        'block';


    clearTimeout(
        showCartMessage.timeout
    );


    showCartMessage.timeout =
        setTimeout(
            function () {

                cartMessage.style.display =
                    'none';

            },
            3000
        );
}


/* =========================
   PALĪGFUNKCIJAS
   ========================= */

function formatPrice(price) {

    return new Intl.NumberFormat(
        'lv-LV',
        {
            style: 'currency',
            currency: 'EUR'
        }
    ).format(price);
}


function escapeCartHtml(value) {

    const element =
        document.createElement('div');


    element.textContent =
        value ?? '';


    return element.innerHTML;
}