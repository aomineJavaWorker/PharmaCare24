<?php

require_once __DIR__ . '/../includes/admin_header.php';

?>


<div class="page-header">

    <div>

        <h1>
            Pasūtījumi
        </h1>

        <p>
            Klientu pasūtījumu un maksājumu pārvaldība.
        </p>

    </div>


    <button
        type="button"
        id="openCreateOrderModal"
        class="primary-admin-button"
    >
        + Pievienot pasūtījumu
    </button>

</div>


<section class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Pasūtījumu saraksts
            </h2>

            <p>
                Kopā:
                <strong id="orderCount">
                    0
                </strong>
            </p>

        </div>

    </div>


    <div
        id="orderMessage"
        class="table-message"
    >
        Ielādē pasūtījumus...
    </div>


    <div class="table-wrapper">

        <table class="admin-table actions-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Klients</th>

                    <th>Zāles</th>

                    <th>Summa</th>

                    <th>Apmaksa</th>

                    <th>Statuss</th>

                    <th>Datums</th>

                    <th>Darbības</th>

                </tr>

            </thead>


            <tbody id="orderTableBody">

            </tbody>

        </table>

    </div>

</section>


<!-- JAUNA PASŪTĪJUMA MODĀLAIS LOGS -->

<div id="createOrderModal" class="admin-modal">
    <div class="admin-modal-content">

        <div class="admin-modal-header">
            <div>
                <h2>Pievienot pasūtījumu</h2>
                <p>Izveidojiet pasūtījumu, kas noformēts uz vietas aptiekā.</p>
            </div>

            <button
                type="button"
                id="closeCreateOrderModal"
                class="admin-modal-close"
                aria-label="Aizvērt"
            >
                ×
            </button>
        </div>

        <div
            id="createOrderFormMessage"
            class="form-message"
        ></div>

        <form id="createOrderForm">

            <div class="form-grid">

                <div class="form-group">
                    <label for="createCustomerName">
                        Klienta vārds un uzvārds *
                    </label>

                    <input
                        type="text"
                        id="createCustomerName"
                        name="customer_name"
                        maxlength="150"
                        required
                    >
                </div>


                <div class="form-group">
                    <label for="createCustomerEmail">
                        E-pasts
                    </label>

                    <input
                        type="email"
                        id="createCustomerEmail"
                        name="customer_email"
                        maxlength="150"
                        placeholder="nav obligāts"
                    >
                </div>

            </div>


            <div class="order-section" style="margin-top: 22px;">

                <div
                    class="admin-panel-header"
                    style="margin-bottom: 12px;"
                >
                    <div>
                        <h3>Pasūtījuma zāles</h3>

                        <p>
                            Var pievienot vairākas dažādas zāles.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="addOrderItemButton"
                        class="secondary-button"
                    >
                        + Pievienot zāles
                    </button>
                </div>


                <div id="createOrderItems"></div>

            </div>


            <div
                class="order-detail"
                style="margin-top: 18px;"
            >
                <span>Kopējā summa</span>

                <strong id="createOrderTotal">
                    0,00 €
                </strong>
            </div>


            <p style="margin-top:16px;color:#758078;font-size:13px;line-height:1.5;">
                Šis pasūtījums tiek noformēts uz vietas aptiekā,
                tāpēc tas uzreiz tiek saglabāts kā
                <strong>Apmaksāts</strong> un
                <strong>Izsniegts</strong>.
                Recepšu zāles šajā formā netiek piedāvātas.
            </p>


            <div class="modal-actions">

                <button
                    type="button"
                    id="cancelCreateOrderModal"
                    class="secondary-button"
                >
                    Atcelt
                </button>

                <button
                    type="submit"
                    class="primary-admin-button"
                >
                    Pievienot pasūtījumu
                </button>

            </div>

        </form>
    </div>
</div>


<!-- PASŪTĪJUMA MODĀLAIS LOGS -->

<div
    id="orderModal"
    class="admin-modal"
>

    <div class="admin-modal-content order-modal-content">


        <div class="admin-modal-header">

            <div>

                <h2 id="orderModalTitle">
                    Pasūtījums
                </h2>

                <p>
                    Pasūtījuma informācija un statuss.
                </p>

            </div>


            <button
                type="button"
                id="closeOrderModal"
                class="admin-modal-close"
                aria-label="Aizvērt"
            >
                ×
            </button>

        </div>


        <div
            id="orderFormMessage"
            class="form-message"
        ></div>


        <div class="order-details-grid">


            <div class="order-detail">

                <span>
                    Klients
                </span>

                <strong id="orderCustomerName">
                    -
                </strong>

            </div>


            <div class="order-detail">

                <span>
                    E-pasts
                </span>

                <strong id="orderCustomerEmail">
                    -
                </strong>

            </div>


            <div class="order-detail">

                <span>
                    Piegādes adrese
                </span>

                <strong id="orderAddress">
                    -
                </strong>

            </div>


            <div class="order-detail">

                <span>
                    Maksājums
                </span>

                <strong id="orderPaymentStatus">
                    -
                </strong>

            </div>


            <div class="order-detail">

                <span>
                    Kopējā summa
                </span>

                <strong id="orderTotal">
                    -
                </strong>

            </div>


            <div class="order-detail">

                <span>
                    Izveidots
                </span>

                <strong id="orderCreatedAt">
                    -
                </strong>

            </div>


        </div>


        <div class="order-section">

            <h3>
                Pasūtītās zāles
            </h3>


            <div id="orderItems">

            </div>

        </div>


        <div class="order-section">

            <h3>
                Stripe
            </h3>


            <div
                id="orderStripeSession"
                class="stripe-session"
            >
                -
            </div>

        </div>


        <form id="orderForm">


            <input
                type="hidden"
                id="orderId"
                name="id"
            >


            <div class="form-group">

                <label for="orderStatus">
                    Pasūtījuma statuss
                </label>


                <select
                    id="orderStatus"
                    name="status"
                    required
                >

                    <option value="Jauns">
                        Jauns
                    </option>

                    <option value="Apstrādē">
                        Apstrādē
                    </option>

                    <option value="Gatavs">
                        Gatavs
                    </option>

                    <option value="Izsniegts">
                        Izsniegts
                    </option>

                    <option value="Atcelts">
                        Atcelts
                    </option>

                </select>

            </div>


            <div class="modal-actions">


                <button
                    type="button"
                    id="cancelOrderModal"
                    class="secondary-button"
                >
                    Atcelt
                </button>


                <button
                    type="submit"
                    class="primary-admin-button"
                >
                    Saglabāt statusu
                </button>


            </div>

        </form>


    </div>

</div>


<script src="../assets/js/admin-orders.js"></script>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>