const themeButton = document.getElementById('themeButton');

const savedTheme = localStorage.getItem('theme');

if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme);
}

updateThemeButton();


themeButton.addEventListener('click', function () {

    const currentTheme =
        document.documentElement.getAttribute('data-theme');

    let newTheme;

    if (currentTheme === 'dark') {
        newTheme = 'light';
    } else {
        newTheme = 'dark';
    }

    document.documentElement.setAttribute(
        'data-theme',
        newTheme
    );

    localStorage.setItem(
        'theme',
        newTheme
    );

    updateThemeButton();
});


function updateThemeButton() {

    const currentTheme =
        document.documentElement.getAttribute('data-theme');

    if (currentTheme === 'dark') {
        themeButton.textContent = '☀';
        themeButton.title = 'Gaišais režīms';
    } else {
        themeButton.textContent = '☾';
        themeButton.title = 'Tumšais režīms';
    }
}