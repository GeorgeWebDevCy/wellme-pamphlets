/**
 * WELLME Pamphlets — Public Interactive JS
 *
 * Handles:
 *   - Module grid card clicks → load pamphlet via AJAX into slide-in modal
 *   - Chapter navigation tabs
 *   - Learning outcome side-panel (Partou pattern)
 *   - Exercise step hotspot dots → open/close step panels (Outremer pattern)
 *   - Flip cards (Sum-Up slide)
 *   - Scroll reveal (IntersectionObserver)
 *   - Keyboard & accessibility
 */
(function () {
    'use strict';

    /* ── Helpers ─────────────────────────────────────────────── */

    function show(el) {
        el.hidden = false;
        el.removeAttribute('hidden');
    }

    function hide(el) {
        el.hidden = true;
    }

    function trapFocus(el) {
        const focusable = el.querySelectorAll(
            'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const first = focusable[0];
        const last  = focusable[focusable.length - 1];
        el.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            if (e.shiftKey) {
                if (document.activeElement === first) { e.preventDefault(); last.focus(); }
            } else {
                if (document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });
        if (first) first.focus();
    }

    /* ── Scroll Reveal ───────────────────────────────────────── */

    function initScrollReveal() {
        const els = document.querySelectorAll('.wellme-scroll-reveal');
        if (!els.length) return;

        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        els.forEach(function (el) { observer.observe(el); });
    }

    /* ── Module Grid → Pamphlet Modal ────────────────────────── */

    function initModuleGrid() {
        var modal = document.getElementById('wellme-pamphlet-modal');
        if (!modal) return;

        var content = document.getElementById('wellme-pamphlet-modal-content');

        // Open modal when a card is clicked
        document.querySelectorAll('.wellme-module-card').forEach(function (card) {
            card.addEventListener('click', openPamphlet);
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPamphlet.call(card); }
            });
        });

        function openPamphlet() {
            var id = this.dataset.pamphletId;
            if (!id) return;

            show(modal);
            document.body.style.overflow = 'hidden';
            content.innerHTML = '<div class="wellme-pamphlet-loading">' + (wellmePamphlets.loading || 'Loading…') + '</div>';

            fetch(wellmePamphlets.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'wellme_load_pamphlet',
                    id: id,
                    nonce: wellmePamphlets.nonce,
                }),
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        content.innerHTML = data.data.html;
                        initPamphletInteractions(content);
                        initScrollReveal();
                        trapFocus(modal);
                    } else {
                        content.innerHTML = '<p style="padding:40px;color:#c00;">' + (data.data || 'Error loading pamphlet.') + '</p>';
                    }
                })
                .catch(function () {
                    content.innerHTML = '<p style="padding:40px;color:#c00;">Could not load pamphlet.</p>';
                });
        }

        // Close modal
        function closeModal() {
            hide(modal);
            document.body.style.overflow = '';
            content.innerHTML = '';
        }

        modal.querySelectorAll('[data-close-modal]').forEach(function (el) {
            el.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) closeModal();
        });
    }

    /* ── Pamphlet Interactions (run after AJAX load) ─────────── */

    function initPamphletInteractions(root) {
        root = root || document;
        initChapterNav(root);
        initOutcomePanels(root);
        initHotspots(root);
    }

    /* ── Chapter Navigation ──────────────────────────────────── */

    function initChapterNav(root) {
        root.querySelectorAll('.wellme-chapter-nav').forEach(function (nav) {
            var btns   = nav.querySelectorAll('.wellme-chapter-btn');
            var parent = nav.closest('.wellme-section-chapters');
            if (!parent) return;
            var panels = parent.querySelectorAll('.wellme-chapter-panel');

            function activate(index) {
                btns.forEach(function (b, i) {
                    b.classList.toggle('is-active', i === index);
                    b.setAttribute('aria-expanded', i === index ? 'true' : 'false');
                });
                panels.forEach(function (p) {
                    var pi = parseInt(p.dataset.chapter, 10);
                    pi === index ? show(p) : hide(p);
                });
            }

            btns.forEach(function (btn, i) {
                btn.addEventListener('click', function () { activate(i); });
            });

            // Open first chapter by default
            if (btns.length) activate(0);
        });
    }

    /* ── Outcome Side Panels (Partou pattern) ────────────────── */

    function initOutcomePanels(root) {
        root.querySelectorAll('.wellme-outcome-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panelId = this.dataset.target;
                var panel   = document.getElementById(panelId);
                if (!panel) return;

                var isOpen = !panel.hidden;

                // Close all outcome panels
                root.querySelectorAll('.wellme-outcome-panel').forEach(hide);
                root.querySelectorAll('.wellme-outcome-btn').forEach(function (b) {
                    b.setAttribute('aria-expanded', 'false');
                });

                if (!isOpen) {
                    show(panel);
                    this.setAttribute('aria-expanded', 'true');
                    trapFocus(panel);
                }
            });
        });

        // Close button inside panel
        root.querySelectorAll('.wellme-outcome-panel-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panel = this.closest('.wellme-outcome-panel');
                if (panel) {
                    hide(panel);
                    var panelId = panel.id;
                    var triggerBtn = root.querySelector('[data-target="' + panelId + '"]');
                    if (triggerBtn) {
                        triggerBtn.setAttribute('aria-expanded', 'false');
                        triggerBtn.focus();
                    }
                }
            });
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            root.querySelectorAll('.wellme-outcome-panel:not([hidden])').forEach(function (panel) {
                hide(panel);
                var triggerBtn = root.querySelector('[data-target="' + panel.id + '"]');
                if (triggerBtn) { triggerBtn.setAttribute('aria-expanded', 'false'); triggerBtn.focus(); }
            });
        });
    }

    /* ── Hotspot dots → Step panels (Outremer pattern) ─────── */

    function initHotspots(root) {
        root.querySelectorAll('.wellme-hotspot-dot').forEach(function (dot) {
            dot.addEventListener('click', function () {
                var panelId = this.dataset.target;
                var panel   = document.getElementById(panelId);
                if (!panel) return;

                var isOpen = !panel.hidden;

                // Close all step panels and reset dots
                root.querySelectorAll('.wellme-step-panel').forEach(hide);
                root.querySelectorAll('.wellme-hotspot-dot').forEach(function (d) {
                    d.setAttribute('aria-expanded', 'false');
                });

                if (!isOpen) {
                    show(panel);
                    this.setAttribute('aria-expanded', 'true');
                    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });

        // Close button inside step panel
        root.querySelectorAll('.wellme-step-panel-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panel = this.closest('.wellme-step-panel');
                if (!panel) return;
                hide(panel);
                var dot = root.querySelector('[data-target="' + panel.id + '"]');
                if (dot) { dot.setAttribute('aria-expanded', 'false'); dot.focus(); }
            });
        });

        // Step prev/next navigation
        root.querySelectorAll('.wellme-step-nav-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var currentId = this.dataset.current;
                var targetId  = this.dataset.target;
                var current   = document.getElementById(currentId);
                var target    = document.getElementById(targetId);
                if (current) hide(current);
                if (target) {
                    show(target);
                    // Sync hotspot dots
                    root.querySelectorAll('.wellme-hotspot-dot').forEach(function (d) {
                        d.setAttribute('aria-expanded', d.dataset.target === targetId ? 'true' : 'false');
                    });
                    target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
    }

    /* ── Flip Cards (Sum-Up) ─────────────────────────────────── */

    function initFlipCards() {
        document.querySelectorAll('.wellme-flipcard').forEach(function (card) {
            function toggle() { card.classList.toggle('is-flipped'); }

            card.addEventListener('click', toggle);
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
            });
        });
    }

    /* ── Full-screen Experience [wellme_experience] ──────────── */

    function initExperience() {
        var exp   = document.getElementById('wellme-experience');
        if (!exp) return;

        var track   = document.getElementById('wellme-experience-track');
        var slides  = Array.from(exp.querySelectorAll('.wellme-experience-slide'));
        var dots    = Array.from(exp.querySelectorAll('.wellme-exp-dot'));
        var prevBtn = exp.querySelector('.wellme-exp-arrow--prev');
        var nextBtn = exp.querySelector('.wellme-exp-arrow--next');
        var counter = exp.querySelector('.wellme-exp-counter-current');
        var drawer     = document.getElementById('wellme-exp-drawer');
        var drawerBody = document.getElementById('wellme-exp-drawer-body');

        var current    = 0;
        var total      = slides.length;
        var drawerOpen = false;
        var touchStartX = 0;

        // ── Slide navigation ──────────────────────────────────

        function goTo(index) {
            if (index < 0 || index >= total) return;

            slides[current].classList.remove('is-active');
            if (dots[current]) {
                dots[current].classList.remove('is-active');
                dots[current].setAttribute('aria-current', 'false');
            }

            current = index;

            slides[current].classList.add('is-active');
            if (dots[current]) {
                dots[current].classList.add('is-active');
                dots[current].setAttribute('aria-current', 'true');
            }

            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            if (counter) counter.textContent = current + 1;
            if (prevBtn) prevBtn.hidden = current === 0;
            if (nextBtn) nextBtn.hidden = current === total - 1;
        }

        goTo(0);

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { goTo(i); });
        });

        // Keyboard — arrows navigate slides; Escape closes drawer
        document.addEventListener('keydown', function (e) {
            if (drawerOpen) {
                if (e.key === 'Escape') closeDrawer();
                return;
            }
            if (e.key === 'ArrowLeft')  goTo(current - 1);
            if (e.key === 'ArrowRight') goTo(current + 1);
        });

        // Touch swipe
        exp.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].clientX;
        }, { passive: true });

        exp.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) {
                dx < 0 ? goTo(current + 1) : goTo(current - 1);
            }
        }, { passive: true });

        // ── Drawer ────────────────────────────────────────────

        exp.querySelectorAll('.wellme-exp-explore-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDrawer(this.dataset.moduleId, this);
            });
        });

        function openDrawer(moduleId, triggerBtn) {
            drawer.removeAttribute('hidden');
            // Reflow needed so the CSS transition fires from the initial state
            drawer.getBoundingClientRect();
            drawer.classList.add('is-open');
            drawerOpen = true;
            document.body.style.overflow = 'hidden';

            exp.querySelectorAll('.wellme-exp-explore-btn').forEach(function (b) {
                b.setAttribute('aria-expanded', b === triggerBtn ? 'true' : 'false');
            });

            // Cache: skip re-fetch if same module already loaded
            if (drawerBody.dataset.loadedId === String(moduleId)) {
                drawerBody.scrollTop = 0;
                focusDrawerClose();
                return;
            }

            drawerBody.innerHTML = '<div class="wellme-pamphlet-loading">' +
                ((typeof wellmePamphlets !== 'undefined' && wellmePamphlets.loading) || 'Loading\u2026') +
                '</div>';

            fetch(wellmePamphlets.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'wellme_load_pamphlet',
                    id:     moduleId,
                    nonce:  wellmePamphlets.nonce,
                }),
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        drawerBody.innerHTML = data.data.html;
                        drawerBody.dataset.loadedId = moduleId;
                        initPamphletInteractions(drawerBody);
                        initScrollReveal();
                        focusDrawerClose();
                    } else {
                        drawerBody.innerHTML = '<p style="padding:40px;color:#c00;">' +
                            (data.data || 'Error loading module.') + '</p>';
                    }
                })
                .catch(function () {
                    drawerBody.innerHTML = '<p style="padding:40px;color:#c00;">Could not load module.</p>';
                });
        }

        function closeDrawer() {
            drawer.classList.remove('is-open');
            drawerOpen = false;
            document.body.style.overflow = '';

            exp.querySelectorAll('.wellme-exp-explore-btn').forEach(function (b) {
                b.setAttribute('aria-expanded', 'false');
            });

            drawer.addEventListener('transitionend', function onEnd() {
                drawer.setAttribute('hidden', '');
                drawer.removeEventListener('transitionend', onEnd);
            });

            // Return focus to the active slide's explore button
            var btn = slides[current] && slides[current].querySelector('.wellme-exp-explore-btn');
            if (btn) btn.focus();
        }

        function focusDrawerClose() {
            var btn = drawer.querySelector('.wellme-exp-drawer-close');
            if (btn) btn.focus();
        }

        var closeBtn = drawer.querySelector('[data-close-drawer]');
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

        // Show a brief keyboard hint on desktop
        if (window.matchMedia('(hover: hover)').matches && total > 1) {
            var hint = document.createElement('p');
            hint.className = 'wellme-exp-hint';
            hint.textContent = '← → to navigate';
            exp.appendChild(hint);
            setTimeout(function () { hint.parentNode && hint.parentNode.removeChild(hint); }, 4500);
        }
    }

    /* ── AJAX handler for pamphlet modal ─────────────────────── */
    // (server-side registered in class-wellme-pamphlets-public.php)

    /* ── Boot ────────────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', function () {
        initScrollReveal();
        initModuleGrid();
        initFlipCards();
        initExperience();

        // If a standalone [wellme_pamphlet] shortcode is on the page (not in modal)
        initPamphletInteractions(document);
    });

})();
