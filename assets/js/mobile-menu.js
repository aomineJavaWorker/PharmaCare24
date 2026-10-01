const menuToggle = document.getElementById('menuToggle');
const mainNavigation = document.getElementById('mainNavigation');


if (menuToggle && mainNavigation) {

    function openMenu() {
        mainNavigation.classList.add('active');
        menuToggle.classList.add('active');

        menuToggle.setAttribute(
            'aria-expanded',
            'true'
        );

        menuToggle.setAttribute(
            'aria-label',
            'Aizvērt izvēlni'
        );
    }


    function closeMenu() {
        mainNavigation.classList.remove('active');
        menuToggle.classList.remove('active');

        menuToggle.setAttribute(
            'aria-expanded',
            'false'
        );

        menuToggle.setAttribute(
            'aria-label',
            'Atvērt izvēlni'
        );
    }


    function toggleMenu() {

        const isOpen =
            mainNavigation.classList.contains('active');

        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    }


    menuToggle.addEventListener(
        'click',
        function (event) {

            event.stopPropagation();

            toggleMenu();
        }
    );


    mainNavigation
        .querySelectorAll('a')
        .forEach(function (link) {

            link.addEventListener(
                'click',
                closeMenu
            );
        });


    document.addEventListener(
        'click',
        function (event) {

            if (
                !mainNavigation.contains(event.target) &&
                !menuToggle.contains(event.target)
            ) {
                closeMenu();
            }
        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeMenu();
            }
        }
    );


    window.addEventListener(
        'resize',
        function () {

            if (window.innerWidth > 900) {
                closeMenu();
            }
        }
    );
}