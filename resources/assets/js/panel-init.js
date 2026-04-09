(function () {
    var html = document.documentElement;
    if (localStorage.getItem('panel-dark-mode') === 'true') {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        var classes = (html.getAttribute('data-dark-classes') || '').split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (classes[i]) html.classList.add(classes[i]);
        }
    }
    var persist = html.getAttribute('data-sidebar-persist') === 'true';
    var initial = html.getAttribute('data-sidebar-initial') || 'expanded';
    if (persist) {
        var stored = localStorage.getItem('panel-sidebar');
        if (stored === 'true') {
            html.setAttribute('data-sidebar-collapsed', 'true');
        } else if (stored === null && initial === 'collapsed') {
            html.setAttribute('data-sidebar-collapsed', 'true');
        }
    } else if (initial === 'collapsed') {
        html.setAttribute('data-sidebar-collapsed', 'true');
    }
})();
