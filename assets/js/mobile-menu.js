const adminMenuToggle =
    document.getElementById(
        'adminMenuToggle'
    );

const adminSidebar =
    document.getElementById(
        'adminSidebar'
    );

const adminSidebarBackdrop =
    document.getElementById(
        'adminSidebarBackdrop'
    );


if (
    adminMenuToggle &&
    adminSidebar &&
    adminSidebarBackdrop
) {

    adminMenuToggle.addEventListener(
        'click',
        function () {

            const isOpen =
                adminSidebar.classList.toggle(
                    'mobile-open'
                );


            adminSidebarBackdrop.classList.toggle(
                'active',
                isOpen
            );


            adminMenuToggle.classList.toggle(
                'active',
                isOpen
            );


            document.body.classList.toggle(
                'admin-menu-open',
                isOpen
            );


            adminMenuToggle.setAttribute(
                'aria-expanded',
                isOpen
                    ? 'true'
                    : 'false'
            );


            adminMenuToggle.setAttribute(
                'aria-label',
                isOpen
                    ? 'Aizvērt izvēlni'
                    : 'Atvērt izvēlni'
            );
        }
    );


    adminSidebarBackdrop.addEventListener(
        'click',
        closeAdminMenu
    );


    adminSidebar
        .querySelectorAll('a')
        .forEach(
            function (link) {

                link.addEventListener(
                    'click',
                    closeAdminMenu
                );
            }
        );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
            ) {

                closeAdminMenu();
            }
        }
    );


    window.addEventListener(
        'resize',
        function () {

            if (
                window.innerWidth > 1024
            ) {

                closeAdminMenu();
            }
        }
    );
}


function closeAdminMenu() {

    if (
        !adminMenuToggle ||
        !adminSidebar ||
        !adminSidebarBackdrop
    ) {

        return;
    }


    adminSidebar.classList.remove(
        'mobile-open'
    );


    adminSidebarBackdrop.classList.remove(
        'active'
    );


    adminMenuToggle.classList.remove(
        'active'
    );


    document.body.classList.remove(
        'admin-menu-open'
    );


    adminMenuToggle.setAttribute(
        'aria-expanded',
        'false'
    );


    adminMenuToggle.setAttribute(
        'aria-label',
        'Atvērt izvēlni'
    );
}