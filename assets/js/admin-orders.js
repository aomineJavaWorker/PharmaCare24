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


loadOrders();


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
   TABULA
   ========================= */

function renderOrders(orders) {

    orderTableBody.innerHTML = '';

    orderCount.textContent =
        orders.length;


    if (orders.length === 0) {

        orderMessage.style.display =
            'block';

        orderMessage.textContent =
            'Pasūtījumi nav atrasti.';

        return;
    }


    orderMessage.style.display =
        'none';


    orders.forEach(function (order) {

        const row =
            document.createElement('tr');


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

    });
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
                ?? 'Neizdevās ielādēt pasūtījumu.'
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
            || order.customer_name
            || '-';


        document.getElementById(
            'orderCustomerEmail'
        ).textContent =
            order.customer_email
            || '-';


        document.getElementById(
            'orderPaymentStatus'
        ).textContent =
            order.payment_status
            || '-';


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
            || '-';


        document.getElementById(
            'orderAddress'
        ).textContent =
            buildAddress(order);


        renderOrderItems(
            result.items ?? []
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


    container.innerHTML = '';


    if (items.length === 0) {

        container.innerHTML = `
            <p>
                Pasūtījumā nav zāļu.
            </p>
        `;

        return;
    }


    items.forEach(function (item) {

        const element =
            document.createElement('div');


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

    });
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
                    ?? 'Neizdevās saglabāt statusu.',
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
                ?? 'Neizdevās dzēst pasūtījumu.'
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
            event.key === 'Escape' &&
            orderModal.classList.contains(
                'active'
            )
        ) {

            closeModal();
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


    return classes[status]
        ?? '';
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


    return classes[status]
        ?? '';
}


/* =========================
   PALĪGFUNKCIJAS
   ========================= */

function buildAddress(order) {

    const parts = [];


    if (order.delivery_address) {

        parts.push(
            order.delivery_address
        );
    }


    if (order.delivery_city) {

        parts.push(
            order.delivery_city
        );
    }


    if (order.delivery_postal_code) {

        parts.push(
            order.delivery_postal_code
        );
    }


    if (order.delivery_country) {

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
            style: 'currency',
            currency: 'EUR'
        }
    ).format(
        Number(value)
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
        document.createElement('div');


    element.textContent =
        value ?? '';


    return element.innerHTML;
}