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

    setInterval(nextSlide, 5000); // Auto slide every 5 seconds
});





