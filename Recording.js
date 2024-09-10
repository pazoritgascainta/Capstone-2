function showContent(section) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach((sec) => sec.classList.remove('active'));

    document.getElementById(section).classList.add('active');
}
