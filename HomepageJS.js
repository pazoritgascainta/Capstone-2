document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('container');
    const loginBtn = document.getElementById('loginBtn');
    const exitBtn = document.getElementById('exitBtn');

    loginBtn.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default anchor behavior
        container.style.display = "block"; // Show the container
        exitBtn.style.display = "block"; // Show the exit button
    });

    exitBtn.addEventListener('click', () => {
        container.style.display = "none"; // Hide the container
        exitBtn.style.display = "none"; // Hide the exit button
    });
});
// SLIDE Transitions Function
var slideIndex = 0;
carousel();

function carousel() {
    var i;
    var x = document.getElementsByName("slider");
    for (i = 0; i < x.length; i++) {
        x[i].checked = false;
    }
    slideIndex++;
    if (slideIndex > x.length) {slideIndex = 1}    
    x[slideIndex-1].checked = true;
    setTimeout(carousel, 10000); // Change slide every 5 seconds
}

//Lightmode/Darkmode Functions
document.getElementById('modeToggle').addEventListener('click', function() {
    var body = document.body;
    if (body.classList.contains('light-mode')) {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
    } else {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
    }
});

//Appointment Form
function showForm() {
    document.getElementById('appointmentForm').style.display = 'block';
}
//backdrop
document.getElementById('loginBtn').addEventListener('click', function() {
    document.getElementById('backdrop').style.display = 'block';
    document.getElementById('container').style.display = 'block';
});

document.getElementById('exitBtn').addEventListener('click', function() {
    document.getElementById('backdrop').style.display = 'none';
    document.getElementById('container').style.display = 'none';
});
//NewsCarousel
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.carousel-inner');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-control-prev');
    const nextButton = document.querySelector('.carousel-control-next');
    let currentIndex = 0;
    const intervalTime = 3000; // Time in milliseconds between automatic transitions
    let interval;

    function updateCarousel() {
        const width = items[0].clientWidth;
        carousel.style.transform = `translateX(${-width * currentIndex}px)`;
    }

    function startAutoSlide() {
        interval = setInterval(() => {
            nextSlide();
        }, intervalTime);
    }

    function resetAutoSlide() {
        clearInterval(interval);
        startAutoSlide();
    }

    function nextSlide() {
        currentIndex = (currentIndex < items.length - 1) ? currentIndex + 1 : 0;
        updateCarousel();
    }

    prevButton.addEventListener('click', function () {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : items.length - 1;
        updateCarousel();
        resetAutoSlide();
    });

    nextButton.addEventListener('click', function () {
        nextSlide();
        resetAutoSlide();
    });

    window.addEventListener('resize', updateCarousel);
    updateCarousel();
    startAutoSlide();
});


