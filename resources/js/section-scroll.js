/**
 * Section-by-section scrolling on desktop.
 *
 * Each wheel gesture moves to the next or previous section with one eased
 * animation. A section taller than the screen scrolls normally until its end
 * is reached, and the following gesture then moves on, so long content (the
 * Saudi Gate journey, forms) is never skipped.
 *
 * Sections are the direct children of <main> plus the footer. Very short
 * blocks (the stats strip) are not stops of their own; they are read as part
 * of the section before them.
 */

const GESTURE_GAP_MS = 180;
const EDGE_TOLERANCE = 4;
const MIN_STOP_RATIO = 0.45;

const easeOutQuart = (t) => 1 - Math.pow(1 - t, 4);

export function sectionScroll(lenis, { onChange } = {}) {
    let locked = false;
    let lastWheelAt = 0;
    let gestureConsumed = false;
    let activeIndex = -1;

    function stops() {
        const viewport = window.innerHeight;
        const elements = [...document.querySelectorAll('main > section, body > footer')];

        return elements.filter((element, index) => index === 0 || element.offsetHeight >= viewport * MIN_STOP_RATIO);
    }

    function topOf(element) {
        return element.getBoundingClientRect().top + window.scrollY;
    }

    // Index of the stop that contains the given scroll position, plus where it starts and ends.
    function locate(position) {
        const list = stops();
        const tops = list.map(topOf);
        let index = 0;

        tops.forEach((top, i) => {
            if (top <= position + EDGE_TOLERANCE) {
                index = i;
            }
        });

        return {
            list,
            tops,
            index,
            start: tops[index],
            end: tops[index + 1] ?? document.documentElement.scrollHeight,
        };
    }

    function goTo(target) {
        locked = true;

        lenis.scrollTo(Math.max(0, Math.round(target)), {
            duration: 1.15,
            easing: easeOutQuart,
            lock: true,
            force: true,
            onComplete: () => {
                locked = false;
                report();
            },
        });
    }

    function goToIndex(index) {
        const { list } = locate(lenis.targetScroll);
        const element = list[Math.min(Math.max(index, 0), list.length - 1)];

        if (element) {
            goTo(topOf(element));
        }
    }

    // Decides what one step in the given direction does. Returns true when it handled the step.
    function step(direction) {
        const viewport = window.innerHeight;
        const position = lenis.targetScroll;
        const { tops, index, start, end } = locate(position);

        if (direction > 0) {
            const sectionBottomInView = end <= position + viewport + EDGE_TOLERANCE;

            if (sectionBottomInView && tops[index + 1] !== undefined) {
                goTo(tops[index + 1]);

                return true;
            }

            return false;
        }

        if (position <= start + EDGE_TOLERANCE && index > 0) {
            // Land on the end of a tall previous section rather than jumping over its content.
            goTo(Math.max(tops[index - 1], start - viewport));

            return true;
        }

        return false;
    }

    // Lets a normal scroll inside a tall section stop exactly at its edge instead of spilling into the next one.
    function clampWithinSection(delta) {
        const viewport = window.innerHeight;
        const position = lenis.targetScroll;
        const { start, end } = locate(position);
        const next = position + delta;

        if (delta > 0 && next + viewport > end) {
            lenis.scrollTo(end - viewport);

            return true;
        }

        if (delta < 0 && next < start) {
            lenis.scrollTo(start);

            return true;
        }

        return false;
    }

    function report() {
        if (!onChange) {
            return;
        }

        const { index, list } = locate(lenis.targetScroll);

        if (index !== activeIndex) {
            activeIndex = index;
            onChange(index, list);
        }
    }

    lenis.options.virtualScroll = ({ deltaY, event }) => {
        if (event.type !== 'wheel' || event.ctrlKey || deltaY === 0) {
            return true;
        }

        // Scrollable areas (textareas, the mobile menu...) keep their own native scroll.
        if (event.target instanceof Element && event.target.closest('[data-lenis-prevent], textarea, select')) {
            return true;
        }

        const now = performance.now();
        const isNewGesture = now - lastWheelAt > GESTURE_GAP_MS;
        lastWheelAt = now;

        if (isNewGesture) {
            gestureConsumed = false;
        }

        // One gesture (including trackpad inertia) moves one section at most.
        if (locked || gestureConsumed) {
            event.preventDefault();

            return false;
        }

        const direction = Math.sign(deltaY);

        if (step(direction)) {
            gestureConsumed = true;
            event.preventDefault();

            return false;
        }

        if (clampWithinSection(deltaY)) {
            gestureConsumed = true;
            event.preventDefault();

            return false;
        }

        return true;
    };

    // Page Up / Page Down / Space follow the same steps; arrow keys keep scrolling line by line.
    window.addEventListener('keydown', (event) => {
        if (event.defaultPrevented || event.altKey || event.ctrlKey || event.metaKey) {
            return;
        }

        if (event.target instanceof Element && event.target.closest('input, textarea, select, button, [contenteditable], [role="dialog"]')) {
            return;
        }

        const direction = { PageDown: 1, PageUp: -1, ' ': event.shiftKey ? -1 : 1 }[event.key];

        if (!direction || locked) {
            return;
        }

        event.preventDefault();

        if (!step(direction)) {
            lenis.scrollTo(lenis.targetScroll + direction * window.innerHeight * 0.85);
        }
    });

    lenis.on('scroll', report);
    report();

    return { goToIndex, stops };
}
