const menuToggle =
    document.getElementById('menuToggle');

const mainNavigation =
    document.getElementById('mainNavigation');


if (menuToggle && mainNavigation) {

    menuToggle.addEventListener(
        'click',
        function () {

            const isOpen =
                mainNavigation.classList.toggle(
                    'active'
                );


            menuToggle.classList.toggle(
                'active',
                isOpen
            );


            menuToggle.setAttribute(
                'aria-expanded',
                isOpen
                    ? 'true'
                    : 'false'
            );


            menuToggle.setAttribute(
                'aria-label',
                isOpen
                    ? 'Aizvērt izvēlni'
                    : 'Atvērt izvēlni'
            );
        }
    );


    /*
     * Aizver izvēlni pēc saites nospiešanas.
     */

    const navigationLinks =
        mainNavigation.querySelectorAll(
            'a'
        );


    navigationLinks.forEach(
        function (link) {

            link.addEventListener(
                'click',
                function () {

                    closeMenu();
                }
            );
        }
    );


    /*
     * Klikšķis ārpus izvēlnes.
     */

    document.addEventListener(
        'click',
        function (event) {

            const clickedInsideMenu =
                mainNavigation.contains(
                    event.target
                );


            const clickedToggle =
                menuToggle.contains(
                    event.target
                );


            if (
                !clickedInsideMenu &&
                !clickedToggle
            ) {

                closeMenu();
            }
        }
    );


    /*
     * Escape aizver izvēlni.
     */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
            ) {

                closeMenu();
            }
        }
    );


    /*
     * Ja ekrāns kļūst desktop izmērā,
     * noņemam mobilās izvēlnes stāvokli.
     */

    window.addEventListener(
        'resize',
        function () {

            if (
                window.innerWidth > 900
            ) {

                closeMenu();
            }
        }
    );
}


function closeMenu() {

    if (
        !menuToggle ||
        !mainNavigation
    ) {

        return;
    }


    mainNavigation.classList.remove(
        'active'
    );


    menuToggle.classList.remove(
        'active'
    );


    menuToggle.setAttribute(
        'aria-expanded',
        'false'
    );


    menuToggle.setAttribute(
        'aria-label',
        'Atvērt izvēlni'
    );
}