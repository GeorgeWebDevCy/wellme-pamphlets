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

    function isWellmeDebugEnabled() {
        const params = new URLSearchParams(window.location.search);
        const flag = params.get('wellme_debug') || params.get('wellmeDebug');

        try {
            if (flag === '1' || flag === 'true') {
                window.localStorage.setItem('wellmeDebug', '1');
                return true;
            }

            if (flag === '0' || flag === 'false') {
                window.localStorage.removeItem('wellmeDebug');
                return false;
            }

            return window.localStorage.getItem('wellmeDebug') === '1';
        } catch (e) {
            return flag === '1' || flag === 'true';
        }
    }

    let wellmeDebugEnabled = isWellmeDebugEnabled();
    let wellmeLogoSpinInterval = 0;

    function wellmePrefersReducedMotion() {
        return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }

    function getWellmeHeroLogo() {
        return document.querySelector('.wellme-experience--reader .wellme-slide-landing.is-active .wellme-landing-hero-media.is-logo-hero .wellme-landing-hero-logo') ||
            document.querySelector('.wellme-experience--reader .wellme-slide-landing .wellme-landing-hero-media.is-logo-hero .wellme-landing-hero-logo');
    }

    function initWellmeLogoDirectSpin() {
        const logo = getWellmeHeroLogo();

        if (!logo || wellmeLogoSpinInterval || wellmePrefersReducedMotion()) return;

        const duration = 5500;
        const start = window.performance ? window.performance.now() : Date.now();

        logo.dataset.wellmeDirectSpin = '1';
        logo.dataset.wellmeSpinTicks = '0';
        logo.style.setProperty('animation', 'none', 'important');
        logo.style.transformOrigin = '50% 50%';
        logo.style.willChange = 'transform';

        function spin() {
            const currentLogo = getWellmeHeroLogo();

            if (!currentLogo || !document.body.contains(currentLogo)) {
                window.clearInterval(wellmeLogoSpinInterval);
                wellmeLogoSpinInterval = 0;
                return;
            }

            if (wellmePrefersReducedMotion()) {
                currentLogo.dataset.wellmeDirectSpin = '0';
                currentLogo.style.removeProperty('animation');
                currentLogo.style.removeProperty('transform');
                currentLogo.style.removeProperty('will-change');
                window.clearInterval(wellmeLogoSpinInterval);
                wellmeLogoSpinInterval = 0;
                return;
            }

            if (currentLogo.closest('.wellme-slide-landing.is-active')) {
                const now = window.performance ? window.performance.now() : Date.now();
                const progress = ((now - start) % duration) / duration;
                const angle = progress * 360;
                const scale = 1 + (0.025 * Math.sin(progress * Math.PI * 2));
                const transform = 'rotate(' + angle.toFixed(2) + 'deg) scale(' + scale.toFixed(3) + ')';

                currentLogo.dataset.wellmeDirectSpin = '1';
                currentLogo.dataset.wellmeSpinTicks = String((Number(currentLogo.dataset.wellmeSpinTicks) || 0) + 1);
                currentLogo.dataset.wellmeSpinTransform = transform;
                currentLogo.style.setProperty('animation', 'none', 'important');
                currentLogo.style.setProperty('transform', transform, 'important');
            }
        }

        spin();
        wellmeLogoSpinInterval = window.setInterval(spin, 16);
    }

    function wellmeDebugElement(selector) {
        const el = document.querySelector(selector);

        if (!el) {
            return {
                selector: selector,
                found: false
            };
        }

        const rect = el.getBoundingClientRect();
        const computed = window.getComputedStyle(el);
        let animations = [];

        if (typeof el.getAnimations === 'function') {
            animations = el.getAnimations().map(function (animation) {
                const timing = animation.effect && typeof animation.effect.getTiming === 'function'
                    ? animation.effect.getTiming()
                    : {};

                return {
                    animationName: animation.animationName || '(css)',
                    currentTime: animation.currentTime,
                    playState: animation.playState,
                    duration: timing.duration,
                    delay: timing.delay,
                    iterations: timing.iterations
                };
            });
        }

        return {
            selector: selector,
            found: true,
            tag: el.tagName.toLowerCase(),
            className: el.className,
            id: el.id || '',
            src: el.currentSrc || el.src || '',
            jsSpinActive: el.dataset.wellmeDirectSpin === '1',
            jsSpinTicks: el.dataset.wellmeSpinTicks || '',
            inlineTransform: el.style.transform || '',
            inlineTransformPriority: el.style.getPropertyPriority('transform') || '',
            parentClass: el.parentElement ? el.parentElement.className : '',
            activeSlide: !!el.closest('.wellme-experience-slide.is-active'),
            inHeroMedia: !!el.closest('.wellme-landing-hero-media'),
            rect: {
                x: Math.round(rect.x),
                y: Math.round(rect.y),
                width: Math.round(rect.width),
                height: Math.round(rect.height)
            },
            computed: {
                display: computed.display,
                visibility: computed.visibility,
                opacity: computed.opacity,
                animationName: computed.animationName,
                animationDuration: computed.animationDuration,
                animationDelay: computed.animationDelay,
                animationIterationCount: computed.animationIterationCount,
                animationPlayState: computed.animationPlayState,
                transform: computed.transform,
                transformOrigin: computed.transformOrigin,
                zIndex: computed.zIndex
            },
            animations: animations
        };
    }

    function wellmeDebugSnapshot(label) {
        if (!wellmeDebugEnabled || !window.console) return;

        const localized = typeof wellmePamphlets !== 'undefined' ? wellmePamphlets : {};
        const snapshot = {
            label: label || 'snapshot',
            version: localized.version || '(unknown)',
            url: window.location.href,
            readyState: document.readyState,
            reducedMotion: !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches),
            activeSlide: (document.querySelector('.wellme-experience-slide.is-active') || {}).className || '',
            hero: wellmeDebugElement('.wellme-landing-hero-media'),
            heroImage: wellmeDebugElement('.wellme-experience--reader .wellme-slide-landing.is-active .wellme-landing-hero-media.is-logo-hero img, .wellme-landing-hero-media img'),
            wellmeColourImage: wellmeDebugElement('img[src*="WellMe-Colour"], img[src*="WellMe-Colour.webp"]'),
            logoSpin: wellmeDebugElement('.wellme-logo-spin'),
            euLogo: wellmeDebugElement('.wellme-landing-eu'),
            euText: wellmeDebugElement('.wellme-landing-eu-text'),
            agreement: wellmeDebugElement('.wellme-landing-agreement')
        };

        if (snapshot.heroImage.found) {
            const logoSummary = {
                label: snapshot.label,
                src: snapshot.heroImage.src,
                isWellmeColourLogo: snapshot.heroImage.src.indexOf('WellMe-Colour') !== -1,
                className: snapshot.heroImage.className,
                animationName: snapshot.heroImage.computed.animationName,
                duration: snapshot.heroImage.computed.animationDuration,
                playState: snapshot.heroImage.computed.animationPlayState,
                transform: snapshot.heroImage.computed.transform,
                jsSpinActive: snapshot.heroImage.jsSpinActive,
                jsSpinTicks: snapshot.heroImage.jsSpinTicks,
                inlineTransform: snapshot.heroImage.inlineTransform,
                inlineTransformPriority: snapshot.heroImage.inlineTransformPriority,
                width: snapshot.heroImage.rect.width,
                height: snapshot.heroImage.rect.height,
                reducedMotion: snapshot.reducedMotion,
                activeSlide: snapshot.heroImage.activeSlide
            };
            const hasAnimation = logoSummary.jsSpinActive || (logoSummary.animationName && logoSummary.animationName !== 'none');
            const isPlaying = logoSummary.jsSpinActive || logoSummary.playState === 'running';
            const logMethod = hasAnimation && isPlaying && logoSummary.width > 0 && logoSummary.height > 0
                ? 'info'
                : 'warn';

            console[logMethod](
                '[WELLME Debug] logo summary: animation=' + logoSummary.animationName +
                ', playState=' + logoSummary.playState +
                ', jsSpin=' + logoSummary.jsSpinActive +
                ', size=' + logoSummary.width + 'x' + logoSummary.height +
                ', WellMe-Colour=' + logoSummary.isWellmeColourLogo,
                logoSummary
            );
        } else {
            console.warn('[WELLME Debug] logo summary: hero image not found', {
                label: snapshot.label,
                activeSlide: snapshot.activeSlide,
                heroFound: snapshot.hero.found
            });
        }

        console.groupCollapsed('[WELLME Debug] ' + snapshot.label + ' v' + snapshot.version);
        console.log(snapshot);

        if (snapshot.heroImage.found) {
            console.table([{
                selector: snapshot.heroImage.selector,
                src: snapshot.heroImage.src,
                animationName: snapshot.heroImage.computed.animationName,
                duration: snapshot.heroImage.computed.animationDuration,
                playState: snapshot.heroImage.computed.animationPlayState,
                transform: snapshot.heroImage.computed.transform,
                inlineTransform: snapshot.heroImage.inlineTransform,
                transformPriority: snapshot.heroImage.inlineTransformPriority,
                jsSpinTicks: snapshot.heroImage.jsSpinTicks,
                width: snapshot.heroImage.rect.width,
                height: snapshot.heroImage.rect.height
            }]);
        }

        const activePartnershipSlide = document.querySelector('.wellme-slide-partnership.is-active');
        if (activePartnershipSlide) {
            const selectedCard = activePartnershipSlide.querySelector('.wellme-partner-card[aria-expanded="true"]');
            const openDetail = activePartnershipSlide.querySelector('.wellme-partner-detail:not([hidden])');
            const partnerStrip = activePartnershipSlide.querySelector('.wellme-partner-strip');
            const partnerGrid = activePartnershipSlide.querySelector('.wellme-partners-grid');
            const openDetailStyle = openDetail ? window.getComputedStyle(openDetail) : null;
            const detailRect = openDetail ? openDetail.getBoundingClientRect() : null;
            const stripRect = partnerStrip ? partnerStrip.getBoundingClientRect() : null;

            console.info('[WELLME Debug] partnership summary:', {
                label: snapshot.label,
                selectedPartnerIndex: selectedCard ? selectedCard.dataset.partnerIndex : '',
                selectedPartnerName: selectedCard ? selectedCard.textContent.trim().replace(/\s+/g, ' ') : '',
                openDetailId: openDetail ? openDetail.id : '',
                detailPosition: openDetailStyle ? openDetailStyle.position : '',
                detailTop: detailRect ? Math.round(detailRect.top) : '',
                detailLeft: detailRect ? Math.round(detailRect.left) : '',
                detailWidth: detailRect ? Math.round(detailRect.width) : '',
                detailHeight: detailRect ? Math.round(detailRect.height) : '',
                stripTop: stripRect ? Math.round(stripRect.top) : '',
                stripLeft: stripRect ? Math.round(stripRect.left) : '',
                stripWidth: stripRect ? Math.round(stripRect.width) : '',
                cardCount: partnerGrid ? partnerGrid.querySelectorAll('.wellme-partner-card').length : 0
            });
        }

        console.groupEnd();
    }

    function wellmeDebugSampleLogoMotion(label) {
        if (!wellmeDebugEnabled || !window.console) return;

        const logo = getWellmeHeroLogo() || document.querySelector('.wellme-landing-hero-media img, .wellme-logo-spin');

        if (!logo) {
            console.warn('[WELLME Debug] motion sample: logo not found', { label: label || 'motion sample' });
            return;
        }

        const samples = [];

        function capture(delay) {
            window.setTimeout(function () {
                const computed = window.getComputedStyle(logo);
                samples.push({
                    delay: delay,
                    transform: computed.transform,
                    inlineTransform: logo.style.transform || '',
                    transformPriority: logo.style.getPropertyPriority('transform') || '',
                    animationName: computed.animationName,
                    playState: computed.animationPlayState,
                    jsSpinTicks: logo.dataset.wellmeSpinTicks || ''
                });

                if (samples.length === 3) {
                    const moving = samples.some(function (sample) {
                        return sample.transform !== samples[0].transform;
                    });

                    console[moving ? 'info' : 'warn']('[WELLME Debug] logo motion sample: moving=' + moving, {
                        label: label || 'motion sample',
                        moving: moving,
                        samples: samples
                    });
                }
            }, delay);
        }

        capture(0);
        capture(350);
        capture(700);
    }

    function initWellmeDebug() {
        window.wellmeDebug = {
            enabled: wellmeDebugEnabled,
            inspect: function (label) {
                wellmeDebugSnapshot(label || 'manual');
            },
            sampleLogoMotion: function (label) {
                wellmeDebugSampleLogoMotion(label || 'manual');
            },
            enable: function () {
                try {
                    window.localStorage.setItem('wellmeDebug', '1');
                } catch (e) {}
                wellmeDebugEnabled = true;
                this.enabled = true;
                wellmeDebugSnapshot('enabled');
            },
            disable: function () {
                try {
                    window.localStorage.removeItem('wellmeDebug');
                } catch (e) {}
                wellmeDebugEnabled = false;
                this.enabled = false;
                if (window.console) {
                    console.info('[WELLME Debug] disabled');
                }
            }
        };

        if (!wellmeDebugEnabled) return;

        console.info('[WELLME Debug] enabled. Use window.wellmeDebug.inspect() for a fresh snapshot, or ?wellme_debug=0 to disable.');
        wellmeDebugSnapshot('dom-ready');
        wellmeDebugSampleLogoMotion('dom-ready');
        window.setTimeout(function () { wellmeDebugSnapshot('after 750ms'); }, 750);
        window.setTimeout(function () { wellmeDebugSnapshot('after 2500ms'); }, 2500);

        document.addEventListener('click', function (event) {
            if (event.target.closest('.wellme-exp-topnav-tab, .wellme-exp-arrow, .wellme-exp-dot')) {
                window.setTimeout(function () { wellmeDebugSnapshot('after slide navigation'); }, 350);
            }
        });
    }

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
        initAssessments(root);
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

    /* ── Outcome Link Cards (Partou pattern — inline panels) ──── */

    function initOutcomePanels(root) {
        root.querySelectorAll('.wellme-outcome-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var panelId = this.dataset.target;
                var panel   = document.getElementById(panelId);
                if (!panel) return;

                var isOpen = !panel.hidden;

                // Close all other outcome panels and reset links
                var parent = this.closest('.wellme-section-outcomes');
                if (parent) {
                    parent.querySelectorAll('.wellme-outcome-detail-inline').forEach(hide);
                    parent.querySelectorAll('.wellme-outcome-link').forEach(function (l) {
                        l.setAttribute('aria-expanded', 'false');
                    });
                }

                if (!isOpen) {
                    show(panel);
                    this.setAttribute('aria-expanded', 'true');
                    // Insert panel right after the links container
                    var linksContainer = this.closest('.wellme-outcomes-links');
                    if (linksContainer && linksContainer.nextSibling !== panel) {
                        linksContainer.after(panel);
                    }
                    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });

        // Close button inside inline panel
        root.querySelectorAll('.wellme-outcome-detail-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panel = this.closest('.wellme-outcome-detail-inline');
                if (panel) {
                    hide(panel);
                    var panelId = panel.id;
                    var trigger = root.querySelector('[data-target="' + panelId + '"]');
                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'false');
                        trigger.focus();
                    }
                }
            });
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            root.querySelectorAll('.wellme-outcome-detail-inline:not([hidden])').forEach(function (panel) {
                hide(panel);
                var trigger = root.querySelector('[data-target="' + panel.id + '"]');
                if (trigger) { trigger.setAttribute('aria-expanded', 'false'); trigger.focus(); }
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

    function initAssessments(root) {
        root.querySelectorAll('.wellme-assessment-form').forEach(function (form) {
            if (form.dataset.assessmentReady === 'true') return;

            form.dataset.assessmentReady = 'true';

            var questions = Array.from(form.querySelectorAll('.wellme-assessment-question'));
            var summary = form.querySelector('.wellme-assessment-summary');
            var localized = typeof wellmePamphlets !== 'undefined' ? wellmePamphlets : {};
            var strings = {
                answerAll: localized.answerAll || 'Please answer all questions before checking your results.',
                correct: localized.correct || 'Correct',
                incorrect: localized.incorrect || 'Incorrect',
                correctAnswer: localized.correctAnswer || 'Correct answer:',
                scorePrefix: localized.scorePrefix || 'Your score:',
            };

            function resetQuestion(question) {
                question.classList.remove('is-correct', 'is-incorrect', 'is-unanswered');

                question.querySelectorAll('.wellme-assessment-option').forEach(function (option) {
                    option.classList.remove('is-selected', 'is-correct', 'is-selected-wrong');
                });

                var feedback = question.querySelector('.wellme-assessment-feedback');
                var status = question.querySelector('.wellme-assessment-feedback-status');

                if (feedback) hide(feedback);
                if (status) status.textContent = '';
            }

            function updateSummary(message, isError) {
                if (!summary) return;
                summary.textContent = message;
                summary.classList.toggle('is-error', !!isError);
                show(summary);
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var unanswered = [];

                questions.forEach(function (question) {
                    resetQuestion(question);
                    if (!question.querySelector('input[type="radio"]:checked')) {
                        unanswered.push(question);
                        question.classList.add('is-unanswered');
                    }
                });

                if (unanswered.length) {
                    updateSummary(strings.answerAll, true);
                    var firstInput = unanswered[0].querySelector('input[type="radio"]');
                    if (firstInput) firstInput.focus();
                    return;
                }

                var correctCount = 0;

                questions.forEach(function (question) {
                    var selected = question.querySelector('input[type="radio"]:checked');
                    var correctOption = question.dataset.correctOption || '';
                    var feedback = question.querySelector('.wellme-assessment-feedback');
                    var status = question.querySelector('.wellme-assessment-feedback-status');

                    question.querySelectorAll('.wellme-assessment-option').forEach(function (option) {
                        var input = option.querySelector('input[type="radio"]');
                        if (!input) return;

                        if (input.checked) option.classList.add('is-selected');
                        if (input.value === correctOption) {
                            option.classList.add('is-correct');
                        } else if (input.checked) {
                            option.classList.add('is-selected-wrong');
                        }
                    });

                    if (selected && selected.value === correctOption) {
                        correctCount += 1;
                        question.classList.add('is-correct');
                        if (status) status.textContent = strings.correct + '.';
                    } else {
                        question.classList.add('is-incorrect');
                        if (status) {
                            status.textContent = strings.incorrect + '. ' + strings.correctAnswer + ' ' + correctOption + '.';
                        }
                    }

                    if (feedback) show(feedback);
                });

                var total = questions.length || 1;
                var percentage = Math.round((correctCount / total) * 100);
                updateSummary(strings.scorePrefix + ' ' + correctCount + '/' + total + ' (' + percentage + '%).', false);
            });

            var resetBtn = form.querySelector('.wellme-assessment-reset');
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    form.reset();
                    questions.forEach(resetQuestion);
                    if (summary) {
                        summary.classList.remove('is-error');
                        summary.textContent = '';
                        hide(summary);
                    }
                });
            }
        });
    }

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
        var popupOverlay = document.getElementById('wellme-popup-overlay');

        var current    = 0;
        var total      = slides.length;
        var touchStartX = 0;

        function syncExperienceViewportHeight() {
            var viewportHeight = window.visualViewport ? window.visualViewport.height : window.innerHeight;
            document.documentElement.style.setProperty('--wellme-vh', (viewportHeight * 0.01) + 'px');
        }

        syncExperienceViewportHeight();
        window.addEventListener('resize', syncExperienceViewportHeight);
        window.addEventListener('orientationchange', syncExperienceViewportHeight);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', syncExperienceViewportHeight);
        }

        // ── Slide navigation ──────────────────────────────────

        var topnavTabs    = Array.from(exp.querySelectorAll('.wellme-exp-topnav-tab'));
        var topnavCounter = exp.querySelector('.wellme-exp-topnav-counter-current');

        function goTo(index) {
            if (index < 0 || index >= total) return;

            slides[current].classList.remove('is-active');
            if (dots[current]) {
                dots[current].classList.remove('is-active');
                dots[current].setAttribute('aria-current', 'false');
            }
            if (topnavTabs[current]) {
                topnavTabs[current].classList.remove('is-active');
                topnavTabs[current].setAttribute('aria-selected', 'false');
            }

            current = index;

            slides[current].classList.add('is-active');
            if (dots[current]) {
                dots[current].classList.add('is-active');
                dots[current].setAttribute('aria-current', 'true');
            }
            if (topnavTabs[current]) {
                topnavTabs[current].classList.add('is-active');
                topnavTabs[current].setAttribute('aria-selected', 'true');
            }

            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            if (counter) counter.textContent = current + 1;
            if (topnavCounter) topnavCounter.textContent = current + 1;
            if (prevBtn) prevBtn.hidden = current === 0;
            if (nextBtn) nextBtn.hidden = current === total - 1;
        }

        goTo(0);

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { goTo(i); });
        });

        // Top nav tab clicks
        topnavTabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { goTo(i); });
        });

        exp.querySelectorAll('[data-experience-goto]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                goTo(parseInt(this.dataset.experienceGoto, 10));
            });
        });

        // Keyboard — arrows navigate slides; Escape closes popup
        document.addEventListener('keydown', function (e) {
            if (!popupOverlay.hidden) {
                if (e.key === 'Escape') closePopupOverlay();
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

        // ── Popup overlay ─────────────────────────────────────

        var popupOverlay = document.getElementById('wellme-popup-overlay');

        function openPopupOverlay(moduleId, triggerBtn) {
            var popupBody = document.getElementById('wellme-popup-body');
            var popupTitle = document.getElementById('wellme-popup-title');
            var popupLabel = document.getElementById('wellme-popup-label');
            var popupSubtitle = document.getElementById('wellme-popup-subtitle');
            var popupMoreInfo = document.getElementById('wellme-popup-more-info');
            var popupDesc = document.getElementById('wellme-popup-desc');
            var popupModnum = document.getElementById('wellme-popup-modnum');

            if (!popupOverlay || !popupBody) return;

            // Set title from trigger
            if (triggerBtn) {
                var titleEl = triggerBtn.closest('.wellme-module-inline-card, .wellme-exp-slide')?.querySelector('h3, .wellme-exp-title');
                if (popupTitle && titleEl) popupTitle.textContent = titleEl.textContent;
            }

            // Open popup
            popupOverlay.removeAttribute('hidden');
            popupOverlay.getBoundingClientRect();
            popupOverlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden';

            // Cache
            if (popupBody.dataset.loadedId === String(moduleId)) {
                popupBody.scrollTop = 0;
                return;
            }

            popupBody.innerHTML = '<div class="wellme-pamphlet-loading">' +
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
                        popupBody.innerHTML = data.data.html;
                        popupBody.dataset.loadedId = moduleId;
                        initPamphletInteractions(popupBody);
                        initScrollReveal();
                    } else {
                        popupBody.innerHTML = '<p style="padding:40px;color:#c00;">' +
                            (data.data || 'Error loading module.') + '</p>';
                    }
                })
                .catch(function () {
                    popupBody.innerHTML = '<p style="padding:40px;color:#c00;">Could not load module.</p>';
                });
        }

        function closePopupOverlay() {
            if (!popupOverlay) return;
            popupOverlay.classList.remove('is-visible');
            document.body.style.overflow = '';
            popupOverlay.addEventListener('transitionend', function onEnd() {
                popupOverlay.setAttribute('hidden', '');
                popupOverlay.removeEventListener('transitionend', onEnd);
            });
        }

        if (popupOverlay) {
            popupOverlay.querySelectorAll('[data-close-popup]').forEach(function (btn) {
                btn.addEventListener('click', function (e) { e.preventDefault(); closePopupOverlay(); });
            });

            // More info toggle
            var moreBtn = document.getElementById('wellme-popup-more-btn');
            var moreInfo = document.getElementById('wellme-popup-more-info');
            if (moreBtn && moreInfo) {
                moreBtn.addEventListener('click', function () {
                    moreInfo.hidden = !moreInfo.hidden;
                });
            }
        }

    }

    /* ── AJAX handler for pamphlet modal ─────────────────────── */
    // (server-side registered in class-wellme-pamphlets-public.php)

    /* ── Partner Card Interactions (Landing slide) ─────────────── */

    function initPartnershipCards(root) {
        root = root || document;

        // Handle both old partnership slide cards and new landing partner cards
        root.querySelectorAll('.wellme-partner-card, .wellme-partner-card--landing').forEach(function (card) {
            card.addEventListener('click', function () {
                var idx = this.dataset.partnerIndex;
                if (idx === undefined || idx === null) return;

                // Try landing detail first, then legacy detail
                var panel = document.getElementById('wellme-partner-detail-landing-' + idx) ||
                            document.getElementById('wellme-partner-detail-' + idx);
                if (!panel) return;

                var isOpen = !panel.hidden;

                // Close all partner details and reset cards
                root.querySelectorAll('.wellme-partner-detail').forEach(function (d) { d.hidden = true; });
                root.querySelectorAll('.wellme-partner-card, .wellme-partner-card--landing').forEach(function (c) {
                    c.setAttribute('aria-expanded', 'false');
                });

                if (!isOpen) {
                    panel.hidden = false;
                    this.setAttribute('aria-expanded', 'true');
                    if (!panel.closest('.wellme-experience--reader')) {
                        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); }
            });
        });

        // Close button inside detail panels
        root.querySelectorAll('.wellme-partner-detail-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var detail = this.closest('.wellme-partner-detail');
                if (detail) {
                    detail.hidden = true;
                    var idx = detail.id.replace('wellme-partner-detail-landing-', '').replace('wellme-partner-detail-', '');
                    var trigger = root.querySelector('.wellme-partner-card[data-partner-index="' + idx + '"], .wellme-partner-card--landing[data-partner-index="' + idx + '"]');
                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'false');
                        trigger.focus();
                    }
                }
            });
        });
    }

    /* ── Module Inline Card Interactions (Slide 4) ───────────── */

    function initModuleInlineCards() {
        var overlay    = document.getElementById('wellme-popup-overlay');
        var popupBody  = document.getElementById('wellme-popup-body');
        var popupTitle = document.getElementById('wellme-popup-title');
        var popupLabel = document.getElementById('wellme-popup-label');
        var popupSubtitle = document.getElementById('wellme-popup-subtitle');
        var popupMoreBtn  = document.getElementById('wellme-popup-more-btn');
        var popupMoreInfo = document.getElementById('wellme-popup-more-info');
        var popupDesc  = document.getElementById('wellme-popup-desc');
        var popupModnum = document.getElementById('wellme-popup-modnum');
        if (!overlay || !popupBody) return;

        document.querySelectorAll('.wellme-module-inline-card').forEach(function (card) {
            card.addEventListener('click', function () {
                var moduleId = this.dataset.moduleId;
                if (!moduleId) return;

                // Get module info from card
                var label = card.querySelector('.wellme-module-inline-number');
                var title = card.querySelector('.wellme-module-inline-title');
                var subtitle = card.querySelector('.wellme-module-inline-subtitle');
                var desc = card.querySelector('.wellme-module-inline-desc');

                if (popupLabel && label) popupLabel.textContent = label.textContent;
                if (popupTitle && title) popupTitle.textContent = title.textContent;
                if (popupSubtitle && subtitle) popupSubtitle.textContent = subtitle.textContent;
                if (popupDesc) popupDesc.textContent = desc ? desc.textContent : '';
                if (popupModnum && label) popupModnum.textContent = label.textContent;
                if (popupMoreInfo) popupMoreInfo.hidden = true;

                // Open popup
                overlay.removeAttribute('hidden');
                overlay.getBoundingClientRect();
                overlay.classList.add('is-visible');
                document.body.style.overflow = 'hidden';

                // Cache check
                if (popupBody.dataset.loadedId === String(moduleId)) {
                    popupBody.scrollTop = 0;
                    return;
                }

                popupBody.innerHTML = '<div class="wellme-pamphlet-loading">' +
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
                            popupBody.innerHTML = data.data.html;
                            popupBody.dataset.loadedId = moduleId;
                            initPamphletInteractions(popupBody);
                            initScrollReveal();
                        } else {
                            popupBody.innerHTML = '<p style="padding:40px;color:#c00;">' +
                                (data.data || 'Error loading module.') + '</p>';
                        }
                    })
                    .catch(function () {
                        popupBody.innerHTML = '<p style="padding:40px;color:#c00;">Could not load module.</p>';
                    });
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); }
            });
        });

        // Close popup handlers
        function closePopup() {
            overlay.classList.remove('is-visible');
            document.body.style.overflow = '';
            overlay.addEventListener('transitionend', function onEnd() {
                overlay.setAttribute('hidden', '');
                overlay.removeEventListener('transitionend', onEnd);
            });
        }

        overlay.querySelectorAll('[data-close-popup]').forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); closePopup(); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay && !overlay.hidden) closePopup();
        });

        // More info toggle
        if (popupMoreBtn && popupMoreInfo) {
            popupMoreBtn.addEventListener('click', function () {
                popupMoreInfo.hidden = !popupMoreInfo.hidden;
            });
        }
    }

    /* ── Boot ────────────────────────────────────────────────── */

    function initOverviewSelector() {
        var overview = document.querySelector('.wellme-overview-content');
        if (!overview) return;

        var buttons = Array.from(overview.querySelectorAll('.wellme-overview-selector, .wellme-mazda-page-tab[data-overview-target]'));
        var panels = Array.from(overview.querySelectorAll('.wellme-overview-section'));
        var label = overview.querySelector('.wellme-overview-active-label');
        var count = overview.querySelector('.wellme-overview-state-count');

        if (!buttons.length || !panels.length) return;

        function activate(button) {
            var targetId = button.dataset.overviewTarget;

            buttons.forEach(function (item) {
                var active = item.dataset.overviewTarget === targetId;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                panel.hidden = panel.id !== targetId;
                if (!panel.hidden) panel.classList.add('is-visible');
            });

            if (label) label.textContent = button.dataset.overviewLabel || '';
            if (count) {
                count.textContent = (button.dataset.overviewIndex || '1') + ' / ' + (button.dataset.overviewTotal || buttons.length);
            }
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () { activate(button); });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollReveal();
        initModuleGrid();
        initFlipCards();
        initExperience();
        initPartnershipCards();
        initModuleInlineCards();
        initOverviewSelector();

        // If a standalone [wellme_pamphlet] shortcode is on the page (not in modal)
        initPamphletInteractions(document);
        initWellmeLogoDirectSpin();
        initWellmeDebug();
    });

})();
