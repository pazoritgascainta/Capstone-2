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
    setTimeout(carousel, 7000); // Change slide every 7 seconds
}

//Appointment Form
function showForm() {
    document.getElementById('appointmentForm').style.display = 'block';
}

const burgerMenu = document.getElementById('burgerMenu');
const navLinks = document.getElementById('navLinks');

burgerMenu.addEventListener('click', () => {
    navLinks.classList.toggle('open');
});


//NewsCarousel
document.addEventListener('DOMContentLoaded', function () {
    const carouselInner = document.querySelector('.carousel-inner');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-control-prev');
    const nextButton = document.querySelector('.carousel-control-next');
    let currentIndex = 0;
    const totalItems = items.length;

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselInner.style.transform = `translateX(${offset}%)`;
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateCarousel();
    }

    nextButton.addEventListener('click', nextSlide);
    prevButton.addEventListener('click', prevSlide);

    // Auto slide every 5 seconds
    setInterval(nextSlide, 5000);
});





