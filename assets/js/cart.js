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


const prescriptionModal =
    document.getElementById(
        'prescriptionModal'
    );

const prescriptionCloseButton =
    document.getElementById(
        'prescriptionCloseButton'
    );

const prescriptionCheckForm =
    document.getElementById(
        'prescriptionCheckForm'
    );

const prescriptionPatientName =
    document.getElementById(
        'prescriptionPatientName'
    );

const prescriptionPersonalCode =
    document.getElementById(
        'prescriptionPersonalCode'
    );

const prescriptionCheckMessage =
    document.getElementById(
        'prescriptionCheckMessage'
    );

const prescriptionContinueButton =
    document.getElementById(
        'prescriptionContinueButton'
    );

const backToCartButton =
    document.getElementById(
        'backToCartButton'
    );


const buyButtons =
    document.querySelectorAll(
        '.medicine-buy-button'
    );


let cart = loadCart();

let verifiedPrescriptionData = null;


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


    invalidatePrescriptionCheck();

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
            item.price *
            item.quantity;


        totalQuantity +=
            item.quantity;


        if (item.prescriptionRequired) {

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


    invalidatePrescriptionCheck();

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


    invalidatePrescriptionCheck();

    saveCart();

    renderCart();
}


function removeFromCart(id) {

    cart =
        cart.filter(
            item =>
                item.id !== id
        );


    invalidatePrescriptionCheck();

    saveCart();

    renderCart();
}


/* =========================
   CART MODAL
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


/* =========================
   TURPINĀT NO GROZA
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
                    item.prescriptionRequired
            );


        if (hasPrescriptionMedicine) {

            openPrescriptionCheck();

            return;
        }


        continueToPayment();
    }
);


/* =========================
   RECEPTES MODAL
   ========================= */

function openPrescriptionCheck() {

    closeCart();


    prescriptionModal.classList.add(
        'active'
    );


    document.body.style.overflow =
        'hidden';


    resetPrescriptionCheck();
}


function closePrescriptionCheck() {

    prescriptionModal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';
}


prescriptionCloseButton.addEventListener(
    'click',
    closePrescriptionCheck
);


prescriptionModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target ===
            prescriptionModal
        ) {

            closePrescriptionCheck();
        }
    }
);


backToCartButton.addEventListener(
    'click',
    function () {

        closePrescriptionCheck();

        openCart();
    }
);


/* =========================
   RECEPTES PĀRBAUDE
   ========================= */

prescriptionCheckForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        prescriptionContinueButton.disabled =
            true;


        verifiedPrescriptionData =
            null;


        showPrescriptionMessage(
            'Pārbauda recepti...',
            'info'
        );


        const items =
            cart.map(
                function (item) {

                    return {
                        medicine_id:
                            item.id,

                        quantity:
                            item.quantity
                    };
                }
            );


        const requestData = {

            patient_name:
                prescriptionPatientName
                    .value
                    .trim(),

            personal_code:
                prescriptionPersonalCode
                    .value
                    .trim(),

            items:
                items
        };


        try {

            const response =
                await fetch(
                    'api/check-prescription.php',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body:
                            JSON.stringify(
                                requestData
                            )
                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                showPrescriptionMessage(
                    result.message
                    ?? 'Recepte nav atrasta.',
                    'error'
                );

                return;
            }


            verifiedPrescriptionData = {

                patientName:
                    requestData.patient_name,

                personalCode:
                    requestData.personal_code,

                prescriptions:
                    result.prescriptions
                    ?? []
            };


            showPrescriptionMessage(
                'Recepte ir derīga. Varat turpināt uz apmaksu.',
                'success'
            );


            prescriptionContinueButton.disabled =
                false;


        } catch (error) {

            console.error(error);


            showPrescriptionMessage(
                'Neizdevās sazināties ar serveri.',
                'error'
            );
        }
    }
);


/* =========================
   PĒC RECEPTES PĀRBAUDES
   ========================= */

prescriptionContinueButton.addEventListener(
    'click',
    function () {

        if (!verifiedPrescriptionData) {

            showPrescriptionMessage(
                'Vispirms pārbaudiet recepti.',
                'error'
            );

            return;
        }


        continueToPayment();
    }
);


/* =========================
   PAYMENT PLACEHOLDER
   ========================= */

async function continueToPayment() {

    if (cart.length === 0) {
        return;
    }


    const hasPrescriptionMedicine =
        cart.some(
            item =>
                item.prescriptionRequired
        );


    if (
        hasPrescriptionMedicine &&
        !verifiedPrescriptionData
    ) {

        showPrescriptionMessage(
            'Vispirms pārbaudiet recepti.',
            'error'
        );

        return;
    }


    const requestData = {

        items:
            cart.map(
                function (item) {

                    return {
                        medicine_id:
                            item.id,

                        quantity:
                            item.quantity
                    };
                }
            )
    };


    /*
     * Personas datus sūtām tikai tad,
     * ja grozā ir recepšu zāles.
     */

    if (hasPrescriptionMedicine) {

        requestData.patient_name =
            verifiedPrescriptionData
                .patientName;


        requestData.personal_code =
            verifiedPrescriptionData
                .personalCode;
    }


    continueCheckoutButton.disabled =
        true;


    prescriptionContinueButton.disabled =
        true;


    try {

        const response =
            await fetch(
                'api/create-checkout-session.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body:
                        JSON.stringify(
                            requestData
                        )
                }
            );


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            if (hasPrescriptionMedicine) {

                showPrescriptionMessage(
                    result.message
                    ?? 'Neizdevās sākt apmaksu.',
                    'error'
                );

            } else {

                showCartMessage(
                    result.message
                    ?? 'Neizdevās sākt apmaksu.',
                    'error'
                );
            }


            continueCheckoutButton.disabled =
                false;


            if (
                hasPrescriptionMedicine
            ) {

                prescriptionContinueButton.disabled =
                    false;
            }


            return;
        }


        if (!result.url) {

            throw new Error(
                'Stripe URL nav saņemts.'
            );
        }


        /*
         * Pārejam uz Stripe Checkout.
         */

        window.location.href =
            result.url;


    } catch (error) {

        console.error(error);


        if (hasPrescriptionMedicine) {

            showPrescriptionMessage(
                'Neizdevās sazināties ar maksājumu serveri.',
                'error'
            );

        } else {

            showCartMessage(
                'Neizdevās sazināties ar maksājumu serveri.',
                'error'
            );
        }


        continueCheckoutButton.disabled =
            false;


        if (
            hasPrescriptionMedicine
        ) {

            prescriptionContinueButton.disabled =
                false;
        }
    }
}


/* =========================
   INVALIDATE CHECK
   ========================= */

function invalidatePrescriptionCheck() {

    verifiedPrescriptionData =
        null;


    if (
        prescriptionContinueButton
    ) {

        prescriptionContinueButton.disabled =
            true;
    }
}


function resetPrescriptionCheck() {

    verifiedPrescriptionData =
        null;


    prescriptionContinueButton.disabled =
        true;


    prescriptionCheckMessage.textContent =
        '';


    prescriptionCheckMessage.className =
        'cart-message';


    prescriptionCheckMessage.style.display =
        'none';
}


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
   CART MESSAGE
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
   PRESCRIPTION MESSAGE
   ========================= */

function showPrescriptionMessage(
    message,
    type
) {

    prescriptionCheckMessage.textContent =
        message;


    prescriptionCheckMessage.className =
        `cart-message ${type}`;


    prescriptionCheckMessage.style.display =
        'block';
}


/* =========================
   ESCAPE
   ========================= */

document.addEventListener(
    'keydown',
    function (event) {

        if (event.key !== 'Escape') {
            return;
        }


        if (
            prescriptionModal.classList.contains(
                'active'
            )
        ) {

            closePrescriptionCheck();

            return;
        }


        if (
            cartModal.classList.contains(
                'active'
            )
        ) {

            closeCart();
        }
    }
);


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