(function () {
    var toggle = document.getElementById('menu-toggle');
    var nav = document.getElementById('header-nav');
    if (!toggle || !nav) {
        return;
    }

    nav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            toggle.checked = false;
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            toggle.checked = false;
        }
    });
})();
