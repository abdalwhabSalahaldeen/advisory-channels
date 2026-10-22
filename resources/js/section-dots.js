/**
 * The vertical dots beside the page: one per section, showing where the
 * visitor is and jumping to a section on click.
 */
export function sectionDots(container, navigation) {
    const sections = navigation.stops();

    const buttons = sections.map((section, index) => {
        const label = section.dataset.sectionLabel
            || section.querySelector('h1, h2')?.textContent.trim().replace(/\s+/g, ' ')
            || `${index + 1}`;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'group relative flex size-6 items-center justify-center';
        button.setAttribute('aria-label', label);
        button.innerHTML = `
            <span class="dot block size-2 rounded-full bg-white/40 transition-all duration-500 group-hover:bg-white"></span>
            <span class="pointer-events-none absolute end-full me-3 whitespace-nowrap rounded-full bg-ink-950/90 px-3 py-1.5 text-xs font-bold text-white opacity-0 shadow-lg transition group-hover:opacity-100 group-focus-visible:opacity-100"></span>
        `;
        button.lastElementChild.textContent = label;
        button.addEventListener('click', () => navigation.goToIndex(index));

        return button;
    });

    container.replaceChildren(...buttons);
    container.hidden = false;

    container.update = (activeIndex) => {
        buttons.forEach((button, index) => {
            const active = index === activeIndex;

            button.toggleAttribute('aria-current', active);
            button.firstElementChild.classList.toggle('h-6!', active);
            button.firstElementChild.classList.toggle('bg-brand-400!', active);
        });
    };

    container.update(navigation.stops().findIndex((section) => section.getBoundingClientRect().bottom > window.innerHeight / 2));
}
