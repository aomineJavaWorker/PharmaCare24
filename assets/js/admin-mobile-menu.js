const adminMenuToggle =
    document.getElementById('adminMenuToggle');

const adminSidebar =
    document.getElementById('adminSidebar');

const adminSidebarBackdrop =
    document.getElementById('adminSidebarBackdrop');


if (
    adminMenuToggle &&
    adminSidebar &&
    adminSidebarBackdrop
) {

    function openAdminMenu() {

        adminSidebar.classList.add(
            'mobile-open'
        );

        adminSidebarBackdrop.classList.add(
            'active'
        );

        adminMenuToggle.classList.add(
            'active'
        );

        document.body.classList.add(
            'admin-menu-open'
        );

        adminMenuToggle.setAttribute(
            'aria-expanded',
            'true'
        );

        adminMenuToggle.setAttribute(
            'aria-label',
            'Aizvērt izvēlni'
        );
    }


    function closeAdminMenu() {

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


    function toggleAdminMenu() {

        const isOpen =
            adminSidebar.classList.contains(
                'mobile-open'
            );

        if (isOpen) {
            closeAdminMenu();
        } else {
            openAdminMenu();
        }
    }


    adminMenuToggle.addEventListener(
        'click',
        toggleAdminMenu
    );


    adminSidebarBackdrop.addEventListener(
        'click',
        closeAdminMenu
    );


    adminSidebar
        .querySelectorAll('a')
        .forEach(function (link) {

            link.addEventListener(
                'click',
                closeAdminMenu
            );
        });


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeAdminMenu();
            }
        }
    );


    window.addEventListener(
        'resize',
        function () {

            if (window.innerWidth > 1024) {
                closeAdminMenu();
            }
        }
    );
}