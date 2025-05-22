document.addEventListener("DOMContentLoaded", () => {
    const visualizer = document.getElementById("visualizer");
    const bars = visualizer.querySelectorAll(".bar");
    let animationInterval;

    function startAnimation() {
        if (!animationInterval) {
            animationInterval = setInterval(() => {
                bars.forEach(bar => {
                    bar.style.height = `${Math.random() * 60 + 20}px`;
                });
            }, 200);
        }
    }

    function stopAnimation() {
        clearInterval(animationInterval);
        animationInterval = null;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startAnimation();
            } else {
                stopAnimation();
            }
        });
    }, {
        threshold: 0.3
    });

    observer.observe(visualizer);
});