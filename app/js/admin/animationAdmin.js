function onEntry(entry) {
    entry.forEach(change => {
        if (change.isIntersecting) {
            change.target.classList.add('_showAnimation');
        } else {
            change.target.classList.remove('_showAnimation');
        }
    });
}
let options = { threshold: [0.0] };
let observer = new IntersectionObserver(onEntry, options);
let elements = document.querySelectorAll('.aniEl');
for (let i = 0; i < elements.length; i++) {
    observer.observe(elements[i]);
}