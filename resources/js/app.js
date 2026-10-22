import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import Swiper from 'swiper';
import { A11y, Keyboard } from 'swiper/modules';
import { networkCanvas } from './network-canvas';
import { sectionDots } from './section-dots';
import { sectionScroll } from './section-scroll';

gsap.registerPlugin(ScrollTrigger);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function smoothScroll() {
    if (reducedMotion) {
        return null;
    }

    const lenis = new Lenis({ lerp: 0.1, anchors: { offset: -80, duration: 1.2 } });

    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    return lenis;
}

// Section-by-section scrolling is for mouse and trackpad users on wide screens; touch keeps native scrolling.
function sectionNavigation(lenis) {
    const desktop = window.matchMedia('(min-width: 1024px) and (pointer: fine)').matches;
    const dots = document.querySelector('[data-section-dots]');

    if (!lenis || !desktop) {
        return;
    }

    const navigation = sectionScroll(lenis, { onChange: (index) => dots?.update?.(index) });

    if (dots && navigation.stops().length >= 3) {
        sectionDots(dots, navigation);
    }
}

function reveals() {
    if (reducedMotion) {
        return;
    }

    gsap.utils.toArray('[data-reveal]').forEach((element) => {
        const delay = Number(element.dataset.revealDelay ?? 0);

        gsap.fromTo(
            element,
            { autoAlpha: 0, y: 32 },
            {
                autoAlpha: 1,
                y: 0,
                duration: 1.1,
                delay,
                ease: 'expo.out',
                scrollTrigger: { trigger: element, start: 'top 88%', once: true },
            },
        );
    });
}

function counters() {
    document.querySelectorAll('[data-counter]').forEach((element) => {
        const target = Number(element.dataset.counter);

        if (!Number.isFinite(target) || reducedMotion) {
            return;
        }

        const state = { value: 0 };

        gsap.to(state, {
            value: target,
            duration: 1.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: element, start: 'top 90%', once: true },
            onUpdate: () => {
                element.textContent = Math.round(state.value).toLocaleString('en');
            },
        });
    });
}

// The vertical line that fills up as the visitor scrolls through the Saudi Gate stages.
function journeyProgress() {
    const journey = document.querySelector('[data-journey]');
    const bar = journey?.querySelector('[data-journey-bar]');

    if (!journey || !bar) {
        return;
    }

    gsap.fromTo(
        bar,
        { scaleY: 0 },
        {
            scaleY: 1,
            ease: 'none',
            scrollTrigger: { trigger: journey, start: 'top 60%', end: 'bottom 60%', scrub: true },
        },
    );

    journey.querySelectorAll('[data-journey-step]').forEach((step) => {
        ScrollTrigger.create({
            trigger: step,
            start: 'top 60%',
            end: 'bottom 60%',
            toggleClass: { targets: step, className: 'is-active' },
        });
    });
}

function sliders() {
    document.querySelectorAll('[data-swiper]').forEach((element) => {
        new Swiper(element, {
            modules: [A11y, Keyboard],
            slidesPerView: 1.15,
            spaceBetween: 16,
            keyboard: { enabled: true },
            breakpoints: {
                640: { slidesPerView: 2.2, spaceBetween: 20 },
                1024: { slidesPerView: 3.3, spaceBetween: 24 },
                1280: { slidesPerView: 4, spaceBetween: 24 },
            },
        });
    });
}

// Runs one setup step; a failure is logged instead of stopping the steps after it.
function safely(setup, onError = () => {}) {
    try {
        return setup();
    } catch (error) {
        console.error(error);
        onError();

        return null;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const lenis = safely(smoothScroll);

    // Reveal targets start hidden (html.js in app.css); never leave them hidden if animating fails.
    safely(reveals, () => document.documentElement.classList.remove('js'));
    safely(counters);
    safely(journeyProgress);
    safely(sliders);
    safely(() => sectionNavigation(lenis));

    document.querySelectorAll('[data-network]').forEach((canvas) => safely(() => networkCanvas(canvas, { reducedMotion })));
});
