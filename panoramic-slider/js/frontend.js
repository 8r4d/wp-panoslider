document.addEventListener("DOMContentLoaded", function () {
    const wrappers = document.querySelectorAll(".panoramic-slider-wrapper");

    wrappers.forEach(function (wrapper) {
        const container = wrapper.querySelector(".panoramic-slider");
        if (!container) return;

        // Drag-to-scroll
        let isDown = false, startX, scrollLeft;

        container.addEventListener("mousedown", function (e) {
            isDown = true;
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        container.addEventListener("mouseleave", function () { isDown = false; });
        container.addEventListener("mouseup", function () { isDown = false; });
        container.addEventListener("mousemove", function (e) {
            if (!isDown) return;
            e.preventDefault();
            container.scrollLeft = scrollLeft - (e.pageX - startX);
        });

        // Touch swipe
        let startTouchX, startScrollLeft;
        container.addEventListener("touchstart", function (e) {
            startTouchX = e.touches[0].pageX;
            startScrollLeft = container.scrollLeft;
        });
        container.addEventListener("touchmove", function (e) {
            container.scrollLeft = startScrollLeft - (e.touches[0].pageX - startTouchX);
        });

        // Left arrow
        const leftBtn = document.createElement("button");
        leftBtn.classList.add("left-arrow");
        wrapper.appendChild(leftBtn);
        leftBtn.onclick = () => { container.scrollLeft -= 100; };

        // Right arrow
        const rightBtn = document.createElement("button");
        rightBtn.classList.add("right-arrow");
        wrapper.appendChild(rightBtn);
        rightBtn.onclick = () => { container.scrollLeft += 100; };

        // Fade in/out on hover
        wrapper.addEventListener("mouseenter", function () {
            leftBtn.style.opacity = 1;
            rightBtn.style.opacity = 1;
        });
        wrapper.addEventListener("mouseleave", function () {
            leftBtn.style.opacity = 0;
            rightBtn.style.opacity = 0;
        });
    });
});
