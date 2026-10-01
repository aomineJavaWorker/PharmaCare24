(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initAdminTables();
        initMedicineCatalog();
    });


    function normalizeText(value) {
        return String(value ?? '')
            .toLocaleLowerCase('lv-LV')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }


    function createTools(options) {
        const toolbar = document.createElement('div');
        toolbar.className = 'list-tools';


        const searchWrap = document.createElement('div');
        searchWrap.className = 'list-search';


        const searchInput = document.createElement('input');

        searchInput.type = 'search';
        searchInput.className = 'list-search-input';
        searchInput.placeholder = options.placeholder;

        searchInput.setAttribute(
            'aria-label',
            options.placeholder
        );


        searchWrap.appendChild(
            searchInput
        );


        const rightSide =
            document.createElement('div');

        rightSide.className =
            'list-tools-right';


        const status =
            document.createElement('span');

        status.className =
            'list-results-count';


        const pageSizeLabel =
            document.createElement('label');

        pageSizeLabel.className =
            'list-page-size';

        pageSizeLabel.textContent =
            'Rādīt:';


        const pageSizeSelect =
            document.createElement('select');

        pageSizeSelect.className =
            'list-page-size-select';

        pageSizeSelect.setAttribute(
            'aria-label',
            'Ierakstu skaits lapā'
        );


        options.pageSizes.forEach(
            function (size) {

                const option =
                    document.createElement('option');

                option.value =
                    String(size);

                option.textContent =
                    String(size);


                if (
                    size ===
                    options.defaultPageSize
                ) {
                    option.selected = true;
                }


                pageSizeSelect.appendChild(
                    option
                );
            }
        );


        pageSizeLabel.appendChild(
            pageSizeSelect
        );


        rightSide.appendChild(
            status
        );

        rightSide.appendChild(
            pageSizeLabel
        );


        toolbar.appendChild(
            searchWrap
        );

        toolbar.appendChild(
            rightSide
        );


        const pagination =
            document.createElement('div');

        pagination.className =
            'list-pagination';


        return {
            toolbar,
            searchInput,
            pageSizeSelect,
            status,
            pagination
        };
    }


    function renderPagination(
        container,
        currentPage,
        totalPages,
        onPageChange
    ) {

        container.innerHTML = '';


        if (totalPages <= 1) {

            container.hidden = true;

            return;
        }


        container.hidden = false;


        const previousButton =
            createPageButton(
                '← Iepriekšējā',
                currentPage === 1,
                function () {

                    onPageChange(
                        currentPage - 1
                    );
                }
            );


        container.appendChild(
            previousButton
        );


        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            const pageButton =
                createPageButton(
                    String(page),
                    false,
                    function () {

                        onPageChange(
                            page
                        );
                    }
                );


            if (
                page ===
                currentPage
            ) {

                pageButton.classList.add(
                    'active'
                );

                pageButton.setAttribute(
                    'aria-current',
                    'page'
                );
            }


            container.appendChild(
                pageButton
            );
        }


        const nextButton =
            createPageButton(
                'Nākamā →',
                currentPage === totalPages,
                function () {

                    onPageChange(
                        currentPage + 1
                    );
                }
            );


        container.appendChild(
            nextButton
        );
    }


    function createPageButton(
        text,
        disabled,
        onClick
    ) {

        const button =
            document.createElement('button');


        button.type = 'button';

        button.className =
            'list-page-button';

        button.textContent =
            text;

        button.disabled =
            disabled;


        button.addEventListener(
            'click',
            onClick
        );


        return button;
    }


    /* =========================================================
       ADMIN TABULAS
       ========================================================= */

    function initAdminTables() {

        const tableWrappers =
            document.querySelectorAll(
                '.admin-panel > .table-wrapper'
            );


        tableWrappers.forEach(
            function (tableWrapper) {

                const table =
                    tableWrapper.querySelector(
                        '.admin-table'
                    );


                const tableBody =
                    table?.querySelector(
                        'tbody'
                    );


                if (
                    !tableBody ||
                    tableWrapper.dataset.listToolsReady === '1'
                ) {
                    return;
                }


                tableWrapper.dataset.listToolsReady =
                    '1';


                const tools =
                    createTools({

                        placeholder:
                            'Meklēt tabulā...',

                        defaultPageSize:
                            10,

                        pageSizes:
                            [5, 10, 15, 25]
                    });


                tableWrapper.parentNode.insertBefore(
                    tools.toolbar,
                    tableWrapper
                );


                tableWrapper.insertAdjacentElement(
                    'afterend',
                    tools.pagination
                );


                let currentPage = 1;

                let scheduledRefresh =
                    false;


                function refresh(
                    resetPage = false
                ) {

                    if (resetPage) {
                        currentPage = 1;
                    }


                    const query =
                        normalizeText(
                            tools.searchInput.value
                        );


                    const pageSize =
                        Number(
                            tools.pageSizeSelect.value
                        );


                    const rows =
                        Array.from(
                            tableBody.querySelectorAll(
                                'tr'
                            )
                        );


                    const filteredRows =
                        rows.filter(
                            function (row) {

                                return normalizeText(
                                    row.textContent
                                ).includes(
                                    query
                                );
                            }
                        );


                    const totalPages =
                        Math.max(
                            1,
                            Math.ceil(
                                filteredRows.length /
                                pageSize
                            )
                        );


                    if (
                        currentPage >
                        totalPages
                    ) {

                        currentPage =
                            totalPages;
                    }


                    rows.forEach(
                        function (row) {

                            row.hidden =
                                true;
                        }
                    );


                    const startIndex =
                        (
                            currentPage - 1
                        ) * pageSize;


                    const endIndex =
                        startIndex +
                        pageSize;


                    filteredRows
                        .slice(
                            startIndex,
                            endIndex
                        )
                        .forEach(
                            function (row) {

                                row.hidden =
                                    false;
                            }
                        );


                    tools.status.textContent =
                        'Atrasti: ' +
                        filteredRows.length;


                    renderPagination(
                        tools.pagination,
                        currentPage,
                        totalPages,
                        function (page) {

                            currentPage =
                                page;

                            refresh(false);
                        }
                    );
                }


                function scheduleRefresh() {

                    if (
                        scheduledRefresh
                    ) {
                        return;
                    }


                    scheduledRefresh =
                        true;


                    requestAnimationFrame(
                        function () {

                            scheduledRefresh =
                                false;

                            refresh(false);
                        }
                    );
                }


                tools.searchInput.addEventListener(
                    'input',
                    function () {

                        refresh(true);
                    }
                );


                tools.pageSizeSelect.addEventListener(
                    'change',
                    function () {

                        refresh(true);
                    }
                );


                const observer =
                    new MutationObserver(
                        scheduleRefresh
                    );


                observer.observe(
                    tableBody,
                    {
                        childList: true
                    }
                );


                refresh(true);
            }
        );
    }


    /* =========================================================
       KLIENTA ZĀĻU KATALOGS
       ========================================================= */

    function initMedicineCatalog() {

        const medicineSection =
            document.getElementById(
                'zales'
            );


        const medicineGrid =
            medicineSection?.querySelector(
                '.medicine-grid'
            );


        if (
            !medicineSection ||
            !medicineGrid
        ) {
            return;
        }


        const cards =
            Array.from(
                medicineGrid.querySelectorAll(
                    '.medicine-card'
                )
            );


        if (
            cards.length === 0
        ) {
            return;
        }


        const tools =
            createTools({

                placeholder:
                    'Meklēt zāles...',

                defaultPageSize:
                    8,

                pageSizes:
                    [4, 8, 12, 16]
            });


        tools.toolbar.classList.add(
            'catalog-list-tools'
        );


        tools.pagination.classList.add(
            'catalog-pagination'
        );


        medicineGrid.insertAdjacentElement(
            'beforebegin',
            tools.toolbar
        );


        medicineGrid.insertAdjacentElement(
            'afterend',
            tools.pagination
        );


        let currentPage = 1;


        function refresh(
            resetPage = false
        ) {

            if (resetPage) {
                currentPage = 1;
            }


            const query =
                normalizeText(
                    tools.searchInput.value
                );


            const pageSize =
                Number(
                    tools.pageSizeSelect.value
                );


            const filteredCards =
                cards.filter(
                    function (card) {

                        return normalizeText(
                            card.textContent
                        ).includes(
                            query
                        );
                    }
                );


            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredCards.length /
                        pageSize
                    )
                );


            if (
                currentPage >
                totalPages
            ) {

                currentPage =
                    totalPages;
            }


            cards.forEach(
                function (card) {

                    card.hidden =
                        true;
                }
            );


            const startIndex =
                (
                    currentPage - 1
                ) * pageSize;


            const endIndex =
                startIndex +
                pageSize;


            filteredCards
                .slice(
                    startIndex,
                    endIndex
                )
                .forEach(
                    function (card) {

                        card.hidden =
                            false;
                    }
                );


            tools.status.textContent =
                'Atrastas zāles: ' +
                filteredCards.length;


            renderPagination(
                tools.pagination,
                currentPage,
                totalPages,
                function (page) {

                    currentPage =
                        page;

                    refresh(false);


                    medicineSection.scrollIntoView(
                        {
                            behavior:
                                'smooth',

                            block:
                                'start'
                        }
                    );
                }
            );
        }


        tools.searchInput.addEventListener(
            'input',
            function () {

                refresh(true);
            }
        );


        tools.pageSizeSelect.addEventListener(
            'change',
            function () {

                refresh(true);
            }
        );


        refresh(true);
    }

})();