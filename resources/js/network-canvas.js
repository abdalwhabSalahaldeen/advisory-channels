/**
 * A slow-drifting network of green nodes for the dark hero sections, echoing
 * the "connected channels" imagery of the brand. Pauses when off screen.
 */
export function networkCanvas(canvas, { reducedMotion = false } = {}) {
    const context = canvas.getContext('2d');

    if (!context) {
        return;
    }

    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
    const linkDistance = 150;
    let width = 0;
    let height = 0;
    let nodes = [];
    let frame = null;

    function resize() {
        const rect = canvas.getBoundingClientRect();

        width = rect.width;
        height = rect.height;
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);

        const count = Math.round(Math.min(90, (width * height) / 14000));

        nodes = Array.from({ length: count }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.25,
            vy: (Math.random() - 0.5) * 0.25,
            r: Math.random() * 1.6 + 0.6,
        }));
    }

    function draw() {
        context.clearRect(0, 0, width, height);

        for (let i = 0; i < nodes.length; i++) {
            const a = nodes[i];

            for (let j = i + 1; j < nodes.length; j++) {
                const b = nodes[j];
                const distance = Math.hypot(a.x - b.x, a.y - b.y);

                if (distance < linkDistance) {
                    context.strokeStyle = `rgba(52, 199, 125, ${0.18 * (1 - distance / linkDistance)})`;
                    context.lineWidth = 1;
                    context.beginPath();
                    context.moveTo(a.x, a.y);
                    context.lineTo(b.x, b.y);
                    context.stroke();
                }
            }

            context.fillStyle = 'rgba(110, 224, 164, 0.7)';
            context.beginPath();
            context.arc(a.x, a.y, a.r, 0, Math.PI * 2);
            context.fill();
        }
    }

    function step() {
        for (const node of nodes) {
            node.x += node.vx;
            node.y += node.vy;

            if (node.x < 0 || node.x > width) node.vx *= -1;
            if (node.y < 0 || node.y > height) node.vy *= -1;
        }

        draw();
        frame = requestAnimationFrame(step);
    }

    function start() {
        if (frame === null && !reducedMotion) {
            frame = requestAnimationFrame(step);
        }
    }

    function stop() {
        if (frame !== null) {
            cancelAnimationFrame(frame);
            frame = null;
        }
    }

    resize();
    draw();

    new IntersectionObserver(([entry]) => (entry.isIntersecting ? start() : stop())).observe(canvas);

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            resize();
            draw();
        }, 150);
    });
}
