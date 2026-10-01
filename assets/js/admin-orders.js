const orderTableBody =
    document.getElementById('orderTableBody');

const orderCount =
    document.getElementById('orderCount');

const orderMessage =
    document.getElementById('orderMessage');


const orderModal =
    document.getElementById('orderModal');

const closeOrderModal =
    document.getElementById('closeOrderModal');

const cancelOrderModal =
    document.getElementById('cancelOrderModal');


const orderForm =
    document.getElementById('orderForm');

const orderFormMessage =
    document.getElementById('orderFormMessage');

const orderId =
    document.getElementById('orderId');

const orderStatus =
    document.getElementById('orderStatus');

const orderModalTitle =
    document.getElementById('orderModalTitle');


const openCreateOrderModal =
    document.getElementById('openCreateOrderModal');

const createOrderModal =
    document.getElementById('createOrderModal');

const closeCreateOrderModal =
    document.getElementById('closeCreateOrderModal');

const cancelCreateOrderModal =
    document.getElementById('cancelCreateOrderModal');

const createOrderForm =
    document.getElementById('createOrderForm');

const createOrderFormMessage =
    document.getElementById('createOrderFormMessage');

const createOrderItems =
    document.getElementById('createOrderItems');

const addOrderItemButton =
    document.getElementById('addOrderItemButton');

const createOrderTotal =
    document.getElementById('createOrderTotal');


let availableMedicines = [];


loadOrders();

loadOrderMedicines();


/* =========================
   IELĀDE
   ========================= */

async function loadOrders() {

    try {

        const response =
            await fetch(
                '../api/orders.php'
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

            throw new Error(
                result.message
            );
        }


        renderOrders(
            result.data
        );


    } catch (error) {

        console.error(error);


        orderMessage.style.display =
            'block';

        orderMessage.textContent =
            'Neizdevās ielādēt pasūtījumus.';

        orderMessage.classList.add(
            'error'
        );
    }
}


/* =========================
   ZĀLES JAUNAM PASŪTĪJUMAM
   ========================= */

async function loadOrderMedicines() {

    try {

        const response =
            await fetch(
                '../api/medicines.php'
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

            throw new Error(
                result.message
            );
        }


        availableMedicines =
            result.data.filter(
                function (medicine) {

                    return (
                        Number(
                            medicine.prescription_required
                        ) === 0
                        &&
                        Number(
                            medicine.stock_quantity
                        ) > 0
                    );
                }
            );


    } catch (error) {

        console.error(error);

        availableMedicines = [];
    }
}


/* =========================
   SELECT OPTIONS
   ========================= */

function createMedicineOptions() {

    let options =
        '<option value="">Izvēlieties zāles</option>';


    availableMedicines.forEach(
        function (medicine) {

            options += `
                <option
                    value="${medicine.id}"
                    data-price="${medicine.price}"
                    data-stock="${medicine.stock_quantity}"
                >
                    ${escapeHtml(
                        medicine.name
                    )} —
                    ${formatPrice(
                        medicine.price
                    )}
                    (noliktavā:
                    ${medicine.stock_quantity})
                </option>
            `;
        }
    );


    return options;
}


/* =========================
   PIEVIENOT ZĀĻU RINDU
   ========================= */

function addCreateOrderItem() {

    const row =
        document.createElement('div');


    row.className =
        'form-grid create-order-item-row';


    row.style.marginBottom =
        '12px';


    row.innerHTML = `

        <div class="form-group">

            <label>
                Zāles *
            </label>


            <select
                class="create-order-medicine"
                required
            >

                ${createMedicineOptions()}

            </select>

        </div>


        <div class="form-group">

            <label>
                Daudzums *
            </label>


            <div
                style="
                    display: flex;
                    gap: 8px;
                    align-items: center;
                "
            >

                <input
                    type="number"
                    class="create-order-quantity"
                    min="1"
                    value="1"
                    required
                >


                <button
                    type="button"
                    class="
                        table-action-button
                        delete-action-button
                        remove-order-item-button
                    "
                    title="Dzēst"
                >
                    Dzēst
                </button>

            </div>

        </div>

    `;


    const select =
        row.querySelector(
            '.create-order-medicine'
        );


    const quantityInput =
        row.querySelector(
            '.create-order-quantity'
        );


    const removeButton =
        row.querySelector(
            '.remove-order-item-button'
        );


    select.addEventListener(
        'change',
        function () {

            const option =
                select.options[
                    select.selectedIndex
                ];


            const stock =
                Number(
                    option?.dataset.stock
                    ?? 0
                );


            if (stock > 0) {

                quantityInput.max =
                    stock;

            } else {

                quantityInput.removeAttribute(
                    'max'
                );
            }


            if (
                stock > 0 &&
                Number(
                    quantityInput.value
                ) > stock
            ) {

                quantityInput.value =
                    stock;
            }


            updateCreateOrderTotal();
        }
    );


    quantityInput.addEventListener(
        'input',
        updateCreateOrderTotal
    );


    removeButton.addEventListener(
        'click',
        function () {

            if (
                createOrderItems.children.length
                <= 1
            ) {

                showCreateOrderMessage(
                    'Pasūtījumā jābūt vismaz vienām zālēm.',
                    'error'
                );

                return;
            }


            row.remove();


            clearCreateOrderMessage();


            updateCreateOrderTotal();
        }
    );


    createOrderItems.appendChild(
        row
    );


    updateCreateOrderTotal();
}


/* =========================
   RESET ZĀĻU RINDAS
   ========================= */

function resetCreateOrderItems() {

    createOrderItems.innerHTML =
        '';


    addCreateOrderItem();
}


/* =========================
   SAVĀC VISAS ZĀLES
   ========================= */

function collectCreateOrderItems() {

    const items = [];


    createOrderItems
        .querySelectorAll(
            '.create-order-item-row'
        )
        .forEach(
            function (row) {

                const medicineId =
                    Number(
                        row.querySelector(
                            '.create-order-medicine'
                        ).value
                    );


                const quantity =
                    Number(
                        row.querySelector(
                            '.create-order-quantity'
                        ).value
                    );


                if (
                    medicineId > 0 &&
                    quantity > 0
                ) {

                    items.push({
                        medicine_id:
                            medicineId,

                        quantity:
                            quantity
                    });
                }
            }
        );


    return items;
}


/* =========================
   APRĒĶINA SUMMU
   ========================= */

function updateCreateOrderTotal() {

    let total = 0;


    createOrderItems
        .querySelectorAll(
            '.create-order-item-row'
        )
        .forEach(
            function (row) {

                const select =
                    row.querySelector(
                        '.create-order-medicine'
                    );


                const quantity =
                    Number(
                        row.querySelector(
                            '.create-order-quantity'
                        ).value
                    ) || 0;


                const option =
                    select.options[
                        select.selectedIndex
                    ];


                const price =
                    Number(
                        option?.dataset.price
                        ?? 0
                    );


                total +=
                    price * quantity;
            }
        );


    createOrderTotal.textContent =
        formatPrice(
            total
        );
}


/* =========================
   + PIEVIENOT ZĀLES
   ========================= */

addOrderItemButton.addEventListener(
    'click',
    function () {

        addCreateOrderItem();
    }
);


/* =========================
   ATVĒRT JAUNU PASŪTĪJUMU
   ========================= */

openCreateOrderModal.addEventListener(
    'click',
    function () {

        createOrderForm.reset();


        clearCreateOrderMessage();


        resetCreateOrderItems();


        createOrderModal.classList.add(
            'active'
        );


        document.body.style.overflow =
            'hidden';
    }
);


/* =========================
   AIZVĒRT CREATE MODAL
   ========================= */

function closeCreateModal() {

    createOrderModal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';


    createOrderForm.reset();


    createOrderItems.innerHTML =
        '';


    createOrderTotal.textContent =
        formatPrice(
            0
        );


    clearCreateOrderMessage();
}


closeCreateOrderModal.addEventListener(
    'click',
    closeCreateModal
);


cancelCreateOrderModal.addEventListener(
    'click',
    closeCreateModal
);


createOrderModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target ===
            createOrderModal
        ) {

            closeCreateModal();
        }
    }
);


/* =========================
   CREATE PASŪTĪJUMS
   ========================= */

createOrderForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const customerName =
            document.getElementById(
                'createCustomerName'
            ).value.trim();


        const customerEmail =
            document.getElementById(
                'createCustomerEmail'
            ).value.trim();


        const items =
            collectCreateOrderItems();


        if (items.length === 0) {

            showCreateOrderMessage(
                'Pievienojiet pasūtījumam vismaz vienas zāles.',
                'error'
            );

            return;
        }


        const data = {

            customer_name:
                customerName,

            customer_email:
                customerEmail,

            items:
                items
        };


        try {

            const response =
                await fetch(
                    '../api/orders.php',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body:
                            JSON.stringify(
                                data
                            )
                    }
                );


            if (
                response.status === 401
            ) {

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

                showCreateOrderMessage(
                    result.message
                    ??
                    'Neizdevās pievienot pasūtījumu.',
                    'error'
                );

                return;
            }


            showCreateOrderMessage(
                result.message,
                'success'
            );


            await loadOrders();


            await loadOrderMedicines();


            setTimeout(
                closeCreateModal,
                700
            );


        } catch (error) {

            console.error(error);


            showCreateOrderMessage(
                'Neizdevās sazināties ar serveri.',
                'error'
            );
        }
    }
);


/* =========================
   CREATE MESSAGE
   ========================= */

function showCreateOrderMessage(
    message,
    type
) {

    createOrderFormMessage.textContent =
        message;


    createOrderFormMessage.className =
        `form-message ${type}`;
}


function clearCreateOrderMessage() {

    createOrderFormMessage.textContent =
        '';


    createOrderFormMessage.className =
        'form-message';
}


/* =========================
   TABULA
   ========================= */

function renderOrders(orders) {

    orderTableBody.innerHTML =
        '';


    orderCount.textContent =
        orders.length;


    if (
        orders.length === 0
    ) {

        orderMessage.style.display =
            'block';


        orderMessage.textContent =
            'Pasūtījumi nav atrasti.';


        return;
    }


    orderMessage.style.display =
        'none';


    orders.forEach(
        function (order) {

            const row =
                document.createElement(
                    'tr'
                );


            row.innerHTML = `

                <td>
                    <strong>
                        #${order.id}
                    </strong>
                </td>


                <td>

                    <strong>
                        ${escapeHtml(
                            order.customer_name
                            ?? 'Klients'
                        )}
                    </strong>

                    <br>

                    <small>
                        ${escapeHtml(
                            order.customer_email
                            ?? '-'
                        )}
                    </small>

                </td>


                <td>
                    ${escapeHtml(
                        order.medicines
                        ?? '-'
                    )}
                </td>


                <td>

                    <strong>
                        ${formatPrice(
                            order.total
                        )}
                    </strong>

                </td>


                <td>

                    <span
                        class="
                            order-badge
                            ${getPaymentClass(
                                order.payment_status
                            )}
                        "
                    >
                        ${escapeHtml(
                            order.payment_status
                            ?? '-'
                        )}
                    </span>

                </td>


                <td>

                    <span
                        class="
                            order-badge
                            ${getOrderStatusClass(
                                order.status
                            )}
                        "
                    >
                        ${escapeHtml(
                            order.status
                        )}
                    </span>

                </td>


                <td>
                    ${formatDate(
                        order.created_at
                    )}
                </td>


                <td>

                    <div class="table-actions">

                        <button
                            type="button"
                            class="
                                table-action-button
                                edit-order-button
                            "
                        >
                            Skatīt
                        </button>


                        <button
                            type="button"
                            class="
                                table-action-button
                                delete-action-button
                                delete-order-button
                            "
                        >
                            Dzēst
                        </button>

                    </div>

                </td>

            `;


            orderTableBody.appendChild(
                row
            );


            row
                .querySelector(
                    '.edit-order-button'
                )
                .addEventListener(
                    'click',
                    function () {

                        openOrder(
                            order.id
                        );
                    }
                );


            row
                .querySelector(
                    '.delete-order-button'
                )
                .addEventListener(
                    'click',
                    function () {

                        deleteOrder(
                            order.id
                        );
                    }
                );
        }
    );
}


/* =========================
   PASŪTĪJUMA ATVĒRŠANA
   ========================= */

async function openOrder(id) {

    clearFormMessage();


    try {

        const response =
            await fetch(
                `../api/orders.php?id=${id}`
            );


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message
                ??
                'Neizdevās ielādēt pasūtījumu.'
            );

            return;
        }


        const order =
            result.data;


        orderId.value =
            order.id;


        orderStatus.value =
            order.status;


        orderModalTitle.textContent =
            `Pasūtījums #${order.id}`;


        document.getElementById(
            'orderCustomerName'
        ).textContent =
            order.delivery_name
            ||
            order.customer_name
            ||
            '-';


        document.getElementById(
            'orderCustomerEmail'
        ).textContent =
            order.customer_email
            ||
            '-';


        document.getElementById(
            'orderPaymentStatus'
        ).textContent =
            order.payment_status
            ||
            '-';


        document.getElementById(
            'orderTotal'
        ).textContent =
            formatPrice(
                order.total
            );


        document.getElementById(
            'orderCreatedAt'
        ).textContent =
            formatDate(
                order.created_at
            );


        document.getElementById(
            'orderStripeSession'
        ).textContent =
            order.stripe_session_id
            ||
            '-';


        document.getElementById(
            'orderAddress'
        ).textContent =
            buildAddress(
                order
            );


        renderOrderItems(
            result.items
            ?? []
        );


        orderModal.classList.add(
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
   PASŪTĪJUMA PRECES
   ========================= */

function renderOrderItems(items) {

    const container =
        document.getElementById(
            'orderItems'
        );


    container.innerHTML =
        '';


    if (
        items.length === 0
    ) {

        container.innerHTML = `
            <p>
                Pasūtījumā nav zāļu.
            </p>
        `;


        return;
    }


    items.forEach(
        function (item) {

            const element =
                document.createElement(
                    'div'
                );


            element.className =
                'order-item-row';


            element.innerHTML = `

                <div>

                    <strong>
                        ${escapeHtml(
                            item.medicine_name
                            ?? 'Zāles'
                        )}
                    </strong>


                    ${
                        item.prescription_number

                            ? `
                                <small>
                                    Recepte:
                                    ${escapeHtml(
                                        item.prescription_number
                                    )}
                                </small>
                            `

                            : ''
                    }

                </div>


                <span>

                    ${item.quantity} ×

                    ${formatPrice(
                        item.unit_price
                    )}

                </span>


                <strong>

                    ${formatPrice(
                        Number(
                            item.unit_price
                        )
                        *
                        Number(
                            item.quantity
                        )
                    )}

                </strong>

            `;


            container.appendChild(
                element
            );
        }
    );
}


/* =========================
   STATUSA SAGLABĀŠANA
   ========================= */

orderForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const data = {

            id:
                Number(
                    orderId.value
                ),

            status:
                orderStatus.value
        };


        try {

            const response =
                await fetch(
                    '../api/orders.php',
                    {
                        method: 'PUT',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body:
                            JSON.stringify(
                                data
                            )
                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                showFormMessage(
                    result.message
                    ??
                    'Neizdevās saglabāt statusu.',
                    'error'
                );

                return;
            }


            showFormMessage(
                result.message,
                'success'
            );


            await loadOrders();


            setTimeout(
                closeModal,
                600
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
   SOFT DELETE
   ========================= */

async function deleteOrder(id) {

    const confirmed =
        confirm(
            `Vai tiešām vēlaties dzēst pasūtījumu #${id}?`
        );


    if (!confirmed) {

        return;
    }


    try {

        const response =
            await fetch(
                '../api/orders.php',
                {
                    method: 'DELETE',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body:
                        JSON.stringify({
                            id: id
                        })
                }
            );


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message
                ??
                'Neizdevās dzēst pasūtījumu.'
            );

            return;
        }


        await loadOrders();


    } catch (error) {

        console.error(error);


        alert(
            'Neizdevās sazināties ar serveri.'
        );
    }
}


/* =========================
   MODAL
   ========================= */

function closeModal() {

    orderModal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';


    clearFormMessage();
}


closeOrderModal.addEventListener(
    'click',
    closeModal
);


cancelOrderModal.addEventListener(
    'click',
    closeModal
);


orderModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target ===
            orderModal
        ) {

            closeModal();
        }
    }
);


document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key !== 'Escape'
        ) {

            return;
        }


        if (
            orderModal.classList.contains(
                'active'
            )
        ) {

            closeModal();
        }


        if (
            createOrderModal.classList.contains(
                'active'
            )
        ) {

            closeCreateModal();
        }
    }
);


/* =========================
   STATUSU KRĀSAS
   ========================= */

function getOrderStatusClass(status) {

    const classes = {

        'Jauns':
            'order-status-new',

        'Apstrādē':
            'order-status-processing',

        'Gatavs':
            'order-status-ready',

        'Izsniegts':
            'order-status-completed',

        'Atcelts':
            'order-status-cancelled'
    };


    return classes[
        status
    ] ?? '';
}


function getPaymentClass(status) {

    const classes = {

        'Apmaksāts':
            'payment-paid',

        'Neapmaksāts':
            'payment-unpaid',

        'Neizdevās':
            'payment-failed'
    };


    return classes[
        status
    ] ?? '';
}


/* =========================
   PALĪGFUNKCIJAS
   ========================= */

function buildAddress(order) {

    const parts = [];


    if (
        order.delivery_address
    ) {

        parts.push(
            order.delivery_address
        );
    }


    if (
        order.delivery_city
    ) {

        parts.push(
            order.delivery_city
        );
    }


    if (
        order.delivery_postal_code
    ) {

        parts.push(
            order.delivery_postal_code
        );
    }


    if (
        order.delivery_country
    ) {

        parts.push(
            order.delivery_country
        );
    }


    return parts.length > 0
        ? parts.join(', ')
        : '-';
}


function formatPrice(value) {

    return new Intl.NumberFormat(
        'lv-LV',
        {
            style:
                'currency',

            currency:
                'EUR'
        }
    ).format(
        Number(
            value
        )
    );
}


function formatDate(value) {

    if (!value) {

        return '-';
    }


    const date =
        new Date(
            value.replace(
                ' ',
                'T'
            )
        );


    return date.toLocaleString(
        'lv-LV'
    );
}


function showFormMessage(
    message,
    type
) {

    orderFormMessage.textContent =
        message;


    orderFormMessage.className =
        `form-message ${type}`;
}


function clearFormMessage() {

    orderFormMessage.textContent =
        '';


    orderFormMessage.className =
        'form-message';
}


function escapeHtml(value) {

    const element =
        document.createElement(
            'div'
        );


    element.textContent =
        value
        ?? '';


    return element.innerHTML;
}