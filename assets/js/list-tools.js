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

    function createCatalogFilters(medicines) {
        const container = document.createElement('div');
        container.className = 'catalog-filters';
        container.setAttribute('role', 'group');
        container.setAttribute('aria-label', 'Kataloga filtri');

        function addField(labelText, input) {
            const label = document.createElement('label');
            label.className = 'catalog-filter-field';

            const title = document.createElement('span');
            title.textContent = labelText;
            label.appendChild(title);
            label.appendChild(input);
            container.appendChild(label);

            return input;
        }

        function createSelect(labelText, choices) {
            const select = document.createElement('select');
            select.className = 'catalog-filter-control';

            choices.forEach(function (choice) {
                const option = document.createElement('option');
                option.value = choice[0];
                option.textContent = choice[1];
                select.appendChild(option);
            });

            return addField(labelText, select);
        }

        function createPriceInput(labelText, placeholder) {
            const input = document.createElement('input');
            input.type = 'number';
            input.min = '0';
            input.step = '0.01';
            input.inputMode = 'decimal';
            input.placeholder = placeholder;
            input.className = 'catalog-filter-control';

            return addField(labelText, input);
        }

        const categories = Array.from(new Set(
            medicines.map(function (medicine) {
                return medicine.category;
            }).filter(Boolean)
        )).sort(function (a, b) {
            return a.localeCompare(b, 'lv');
        });

        const category = createSelect(
            'Kategorija',
            [['', 'Visas kategorijas']].concat(
                categories.map(function (name) {
                    return [name, name];
                })
            )
        );

        const minPrice = createPriceInput('Cena no (€)', '0.00');
        const maxPrice = createPriceInput('Cena līdz (€)', '50.00');

        const prescription = createSelect('Recepte', [
            ['', 'Visas zāles'],
            ['no', 'Bez receptes'],
            ['yes', 'Recepšu zāles']
        ]);

        const availability = createSelect('Pieejamība', [
            ['', 'Visas'],
            ['available', 'Pieejamas'],
            ['unavailable', 'Nav pieejamas']
        ]);

        const sort = createSelect('Kārtot pēc', [
            ['default', 'Sākotnējā secība'],
            ['price-asc', 'Cena: no zemākās'],
            ['price-desc', 'Cena: no augstākās'],
            ['name-asc', 'Nosaukums: A–Z'],
            ['name-desc', 'Nosaukums: Z–A']
        ]);

        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'catalog-filter-reset';
        clearButton.textContent = 'Notīrīt filtrus';
        container.appendChild(clearButton);

        return {
            container,
            category,
            minPrice,
            maxPrice,
            prescription,
            availability,
            sort,
            clearButton
        };
    }


    function initMedicineCatalog() {
        const medicineSection = document.getElementById('zales');
        const medicineGrid = medicineSection?.querySelector('.medicine-grid');

        if (!medicineSection || !medicineGrid) {
            return;
        }

        const cards = Array.from(
            medicineGrid.querySelectorAll('.medicine-card')
        );

        if (cards.length === 0) {
            return;
        }

        const medicines = cards.map(function (card, index) {
            const buyButton = card.querySelector('.medicine-buy-button');

            return {
                card,
                originalIndex: index,
                name: card.querySelector('h3')?.textContent.trim() || '',
                category: card.querySelector('.medicine-category')
                    ?.textContent.trim() || '',
                price: Number(buyButton?.dataset.medicinePrice || 0),
                prescription: buyButton?.dataset.prescriptionRequired === '1',
                available: Number(buyButton?.dataset.medicineStock || 0) > 0,
                searchText: normalizeText(card.textContent)
            };
        });

        const tools = createTools({
            placeholder: 'Meklēt zāles...',
            defaultPageSize: 8,
            pageSizes: [4, 8, 12, 16]
        });

        tools.toolbar.classList.add('catalog-list-tools');
        tools.status.setAttribute('aria-live', 'polite');
        tools.pagination.classList.add('catalog-pagination');

        const filters = createCatalogFilters(medicines);

        const emptyMessage = document.createElement('p');
        emptyMessage.className = 'catalog-empty';
        emptyMessage.textContent =
            'Zāles nav atrastas. Mainiet meklēšanu vai notīriet filtrus.';
        emptyMessage.hidden = true;

        medicineGrid.insertAdjacentElement('beforebegin', tools.toolbar);
        medicineGrid.insertAdjacentElement('beforebegin', filters.container);
        medicineGrid.insertAdjacentElement('afterend', emptyMessage);
        emptyMessage.insertAdjacentElement('afterend', tools.pagination);

        let currentPage = 1;

        function compareNames(a, b) {
            return a.name.localeCompare(b.name, 'lv', {
                sensitivity: 'base'
            });
        }

        function compareMedicines(a, b) {
            let result = 0;

            switch (filters.sort.value) {
                case 'price-asc':
                    result = a.price - b.price;
                    break;
                case 'price-desc':
                    result = b.price - a.price;
                    break;
                case 'name-asc':
                    result = compareNames(a, b);
                    break;
                case 'name-desc':
                    result = compareNames(b, a);
                    break;
                default:
                    return a.originalIndex - b.originalIndex;
            }

            return result || a.originalIndex - b.originalIndex;
        }

        function refresh(resetPage = false) {
            if (resetPage) {
                currentPage = 1;
            }

            const query = normalizeText(tools.searchInput.value);
            const pageSize = Number(tools.pageSizeSelect.value) || 8;
            const minPrice = filters.minPrice.value === ''
                ? null : Number(filters.minPrice.value);
            const maxPrice = filters.maxPrice.value === ''
                ? null : Number(filters.maxPrice.value);

            const sortedMedicines = medicines.slice().sort(compareMedicines);

            const filteredMedicines = sortedMedicines.filter(function (medicine) {
                if (!medicine.searchText.includes(query)) {
                    return false;
                }

                if (filters.category.value &&
                    medicine.category !== filters.category.value) {
                    return false;
                }

                if (minPrice !== null && medicine.price < minPrice) {
                    return false;
                }

                if (maxPrice !== null && medicine.price > maxPrice) {
                    return false;
                }

                if (filters.prescription.value === 'yes' &&
                    !medicine.prescription) {
                    return false;
                }

                if (filters.prescription.value === 'no' &&
                    medicine.prescription) {
                    return false;
                }

                if (filters.availability.value === 'available' &&
                    !medicine.available) {
                    return false;
                }

                if (filters.availability.value === 'unavailable' &&
                    medicine.available) {
                    return false;
                }

                return true;
            });

            const totalPages = Math.max(
                1,
                Math.ceil(filteredMedicines.length / pageSize)
            );

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            sortedMedicines.forEach(function (medicine) {
                medicine.card.hidden = true;
                medicineGrid.appendChild(medicine.card);
            });

            const startIndex = (currentPage - 1) * pageSize;

            filteredMedicines.slice(
                startIndex,
                startIndex + pageSize
            ).forEach(function (medicine) {
                medicine.card.hidden = false;
            });

            emptyMessage.hidden = filteredMedicines.length > 0;
            tools.status.textContent =
                'Atrastas zāles: ' + filteredMedicines.length +
                ' no ' + medicines.length;

            renderPagination(
                tools.pagination,
                currentPage,
                totalPages,
                function (page) {
                    currentPage = page;
                    refresh(false);
                    medicineSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            );
        }

        tools.searchInput.addEventListener('input', function () {
            refresh(true);
        });

        tools.pageSizeSelect.addEventListener('change', function () {
            refresh(true);
        });

        [
            filters.category,
            filters.prescription,
            filters.availability,
            filters.sort
        ].forEach(function (select) {
            select.addEventListener('change', function () {
                refresh(true);
            });
        });

        [filters.minPrice, filters.maxPrice].forEach(function (input) {
            input.addEventListener('input', function () {
                refresh(true);
            });
        });

        filters.clearButton.addEventListener('click', function () {
            tools.searchInput.value = '';
            tools.pageSizeSelect.value = '8';
            filters.category.value = '';
            filters.minPrice.value = '';
            filters.maxPrice.value = '';
            filters.prescription.value = '';
            filters.availability.value = '';
            filters.sort.value = 'default';
            refresh(true);
        });

        refresh(true);
    }

})();