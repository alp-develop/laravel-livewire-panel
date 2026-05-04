(function () {
    var html = document.documentElement;

    function getBool(key, fallback) {
        var val = html.getAttribute('data-' + key);
        if (val === null) return fallback;
        return val === 'true';
    }

    window.togglePanelSidebar = function () {
        if (window.innerWidth <= 768) {
            var isOpen = html.getAttribute('data-sidebar-mobile-open') === 'true';
            if (isOpen) {
                html.removeAttribute('data-sidebar-mobile-open');
            } else {
                html.setAttribute('data-sidebar-mobile-open', 'true');
            }
        } else {
            if (!getBool('sidebar-collapsible', true)) return;
            var isCollapsed = html.getAttribute('data-sidebar-collapsed') === 'true';
            if (isCollapsed) {
                html.removeAttribute('data-sidebar-collapsed');
                if (getBool('sidebar-persist', true)) localStorage.setItem('panel-sidebar', 'false');
            } else {
                html.setAttribute('data-sidebar-collapsed', 'true');
                if (getBool('sidebar-persist', true)) localStorage.setItem('panel-sidebar', 'true');
            }
        }
    };

    window.closeMobileSidebar = function () {
        html.removeAttribute('data-sidebar-mobile-open');
    };

    window.togglePanelDarkMode = function () {
        var active = html.getAttribute('data-theme') !== 'dark';
        if (active) {
            html.setAttribute('data-theme', 'dark');
            html.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('panel-dark-mode', 'true');
        } else {
            html.removeAttribute('data-theme');
            html.removeAttribute('data-bs-theme');
            localStorage.setItem('panel-dark-mode', 'false');
        }
        var classes = (html.getAttribute('data-dark-classes') || '').split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (classes[i]) html.classList.toggle(classes[i], active);
        }
        window.dispatchEvent(new CustomEvent('panel:dark-mode', { detail: { active: active } }));
        var dispatch = html.getAttribute('data-dark-dispatch');
        if (dispatch && typeof Livewire !== 'undefined') {
            Livewire.dispatch(dispatch, { active: active });
        }
        var callback = html.getAttribute('data-dark-callback');
        if (callback && typeof window[callback] === 'function') {
            window[callback](active);
        }
    };

    document.addEventListener('alpine:init', function () {
        Alpine.data('panelChart', function () {
            return {
                init: function () {
                    var el = this.$el;
                    var canvas = el.querySelector('canvas');
                    if (!canvas || typeof Chart === 'undefined') return;
                    if (canvas._chart) canvas._chart.destroy();
                    try {
                        canvas._chart = new Chart(canvas, {
                            type: el.dataset.ctype,
                            data: JSON.parse(el.dataset.cfg),
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    } catch (e) {}
                }
            };
        });
    });

    function applyPanelState() {
        var persist = getBool('sidebar-persist', true);
        var initial = html.getAttribute('data-sidebar-initial') || 'expanded';
        var stored = persist ? localStorage.getItem('panel-sidebar') : null;

        if (stored === 'true') {
            html.setAttribute('data-sidebar-collapsed', 'true');
        } else if (stored === 'false') {
            html.removeAttribute('data-sidebar-collapsed');
        } else if (initial === 'collapsed') {
            html.setAttribute('data-sidebar-collapsed', 'true');
        } else {
            html.removeAttribute('data-sidebar-collapsed');
        }

        var isDark = localStorage.getItem('panel-dark-mode') === 'true';
        if (isDark) {
            html.setAttribute('data-theme', 'dark');
            html.setAttribute('data-bs-theme', 'dark');
        } else {
            html.removeAttribute('data-theme');
            html.removeAttribute('data-bs-theme');
        }
        var classes = (html.getAttribute('data-dark-classes') || '').split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (classes[i]) html.classList.toggle(classes[i], isDark);
        }
    }

    var sidebarHoverWasOpen = false;

    document.addEventListener('livewire:navigating', function (e) {
        var inner = document.querySelector('.panel-sidebar-inner');
        sidebarHoverWasOpen = inner ? inner.classList.contains('panel-sidebar--hover-open') : false;

        e.detail.onSwap(function () {
            applyPanelState();
            if (sidebarHoverWasOpen
                && html.getAttribute('data-sidebar-hover-expand') === 'true'
                && html.getAttribute('data-sidebar-collapsed') === 'true'
            ) {
                var newInner = document.querySelector('.panel-sidebar-inner');
                if (newInner) newInner.classList.add('panel-sidebar--hover-open');
            }
            if (html.getAttribute('data-page-transition') === 'fade') {
                var content = document.querySelector('.panel-content');
                if (content) {
                    content.classList.add('panel-page-entering');
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            content.classList.remove('panel-page-entering');
                        });
                    });
                }
            }
        });
    });

    function initBackToTop() {
        var btn = document.getElementById('panelBackToTop');
        var main = document.querySelector('.panel-main');
        if (btn && main) {
            main.addEventListener('scroll', function () {
                btn.classList.toggle('visible', main.scrollTop > 300);
            });
        }
    }

    function initSidebarHoverExpand() {
        if (html.getAttribute('data-sidebar-hover-expand') !== 'true') return;
        var sidebar = document.querySelector('.panel-sidebar');
        if (!sidebar) return;
        var inner = sidebar.querySelector('.panel-sidebar-inner');
        if (!inner) return;
        sidebar.addEventListener('mouseenter', function () {
            if (html.getAttribute('data-sidebar-collapsed') === 'true') {
                inner.classList.add('panel-sidebar--hover-open');
            }
        });
        sidebar.addEventListener('mouseleave', function () {
            inner.classList.remove('panel-sidebar--hover-open');
        });
    }

    initBackToTop();
    initSidebarHoverExpand();
    document.addEventListener('livewire:navigated', function () {
        initBackToTop();
        initSidebarHoverExpand();
        if (html.getAttribute('data-page-transition') === 'fade') {
            var content = document.querySelector('.panel-content');
            if (content) content.classList.remove('panel-page-entering');
        }
    });

    document.addEventListener('visibilitychange', function () {
        window.dispatchEvent(new Event(document.hidden ? 'offline' : 'online'));
    });

    document.addEventListener('livewire:init', function () {
        Livewire.interceptRequest(function (interceptor) {
            interceptor.onError(function (data) {
                if (data.response && data.response.status >= 500) {
                    data.preventDefault();
                    window.location.reload();
                }
            });
        });
    });
})();
