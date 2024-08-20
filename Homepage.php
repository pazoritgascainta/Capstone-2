<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="HomepageStyle.css">
    <link rel="stylesheet" href="HomepageImgSlider.css">
    <link rel="stylesheet" href="HomepageAnnouncement.css">
    <link rel="stylesheet" href="HomepageaboutUs.css">
    <link rel="stylesheet" href="HomepageHeader.css">
    <link rel="stylesheet" href="HomepageLoginForm.css">
    <link rel="stylesheet" href="HomepageAmenities.css">
    <link rel="stylesheet" href="HomepageLogo.css">
    <title>St. Monique</title>
</head>

<body>
    <div class="logo">
        <p>St. Monique Valais Homeowners' Association</p>
    </div>
    <section class = "HomeLogo">
    <div class="logoo">
    <a href="Homepage.php"><img src="monique logo.png" alt="Your Logo"></a>
    </div>
    </section>
    <header>
        <nav>
            <ul>
                <!-- Updated navigation items -->
                <li><a href="Homepage.php">Home</a></li>
                <li><a href="#" id="loginBtn">Login</a></li>
                <li><a href="ContacUS.php">Contact</a></li>
                <li><a href="Amenities.php">Amenities</a></li>
                <!-- New login button -->
                <div>
                </div>
        </nav>
    </header>
    
    <div id="backdrop" style="display: none;"></div>

    <div class="container" id="container" style="display: none;">
        <div class="form-container sign-up">
            <form>
                <!-- Leave this form untouched -->
            </form>
        </div>
        <div class="form-container sign-in">
            <form>
                <h1>Sign In</h1>
                <span>use your email & password provided by Admin</span>
                <input type="email" placeholder="Email">
                <input type="password" placeholder="Password">
                <a href="ForgetPw.php" class="forgetpw">Forgot Your Password?</a>
                <a href="dashuser.php"><button type="button">Login</button></a>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                </div>
                <div class="exit-btn-container">
                    <button id="exitBtn" class="exit-btn" style="display: none;">X</button> <!-- Exit button -->
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome to St. Monique Valais!</h1>
                    <p>Discover the perfect blend of luxury and community living.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Img Slider Section -->
    <section class="ImageSlider1">
    <div class="ImageSlider">
        <input type="radio" id="trigger1" name="slider" checked autofocus>
        <label for="trigger1"><span class="sr-only"></span></label>
        <div class="slide bg1">
            <div class="description">
                <h3>St.Monique Landmark</h3>
            </div>
        </div>

        <input type="radio" id="trigger2" name="slider">
        <label for="trigger2"><span class="sr-only"></span></label>
        <div class="slide bg2">
            <div class="description">
                <h3>St.Monique School</h3>
            </div>
        </div>

        <input type="radio" id="trigger3" name="slider">
        <label for="trigger3"><span class="sr-only"></span></label>
        <div class="slide bg3">
            <div class="description">
                <h3>St.Monique Church</h3>
            </div>
        </div>

        <input type="radio" id="trigger4" name="slider">
        <label for="trigger4"><span class="sr-only"></span></label>
        <div class="slide bg4">
            <div class="description">
                <h3>Clubhouse</h3>
            </div>
        </div>

        <input type="radio" id="trigger5" name="slider">
        <label for="trigger5"><span class="sr-only"></span></label>
        <div class="slide bg5">
            <div class="description">
                <h3>St.Monique Valais</h3>
            </div>
        </div>
    </div>
</section>

     <!-- Images Highlights -->
    <section class="Amenities1">
        <div class="Amenities">
            <h2>St Monique Valais' Amenities</h2>
            <ul>
                <li>
                    <img src="SwimmingPool.jpg" alt="Swimming Pool">
                    <div class="text">
                        <h3>Swimming Pool</h3>
                    </div>
                </li>
                <li><img src="courtth1.jpg" alt="Basketball Court">
                    
                    <div class="text">
                        <h3>Basketball Court</h3>
                    </div>
                </li>
                <li><img src="Playground.jpg" alt="Playground">
                    <div class="text">
                        <h3>Playground</h3>
                    </div>
                </li>
                <li><img src="clubhouse.jpg" alt="Clubhouse">
                    <div class="text">
                        <h3>Cafe Delfino</h3>
                    </div>
                </li>                            
            </ul>
        </div>
    </section>
    <h1 style="text-align: center;">St.Monique Valais HOA Drone Fly Over</h1>
<div class="video-container">
<video autoplay muted controls>
    <source src="StMoniqueValais2.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>
</div>
    <div class="announcement-container">
        <div class="announcement-main">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://thumbs.dreamstime.com/b/under-maintenance-detailed-illustration-grungy-maintanance-construction-background-61865170.jpg" alt="Announcement Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="Meeting.jpg" alt="Announcement Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="basketball-poster.jpg" alt="Announcement Image 3">
                    </div>
                </div>
                <a class="carousel-control-prev" role="button">
                    <span class="carousel-control-prev-icon" aria-hidden="false">&lt;</span>
                </a>
                <a class="carousel-control-next" role="button">
                    <span class="carousel-control-next-icon" aria-hidden="true">&gt;</span>
                </a>
            </div>
        </div>
        <div class="announcement-news">
            <h3>Latest Announcements</h3>
            <ul>
                <li>Annual General Meeting <span>May 22, 2024</span></li>
                <li>We will be holding our Annual General Meeting on June 15, 2024. All homeowners are encouraged to attend. <span>May 22, 2024</span></li>
                <li>The community pool will be closed for maintenance from July 1 to July 5, 2024. We apologize for any inconvenience. <span>May 20, 2024</span></li>
                <li>Our annual Basketball League will start on August 1, 2024. Register your team by July 15, 2024 <span>May 16, 2024</span></li>
                <li>We are excited to announce a Summer Concert on July 20, 2024. More details will be shared soon.<span>May 14, 2024</span></li>
            </ul>
        </div>
    </div>
    <footer>
        <div class="footer-container">
            <div id="aboutUs">
                <h2>About Us</h2>
                <img src="monique logo.png" width="500" height="225" alt="St Monique Valais Logo">
                <h2>St Monique Valais Homeowners Association</h2>
                <p>Welcome to St. Monique Valais, a beacon of modern living nestled in the heart of our region.
                    Established in 2005 by a visionary in the real estate industry, our community stands as a testament to meticulous planning, upscale amenities, and well-designed homes.
                     Our residents don’t just live here they actively participate in shaping the community through decision-making processes and engaging activities.
                     Experience the hallmark of modern living at St. Monique Valais!.</p>
            <ul>
            <h2>Contact Us</h2>
                <li><i class="fa fa-envelope"></i> Email: saintmoniquev@gmail.com</li>
                <li><i class="fa fa-phone"></i> Phone: 0917 719 7908</li>

                <li><a href="https://www.facebook.com/saintmoniquevalais"><i class="fa-brands fa-square-facebook fa-sm"></i></a></li>
            </ul>
        </div>
            <div class="location-map">
                <h2>Our Location</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.2986102152097!2d121.18476209999999!3d14.509480000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c16a37b4ca23%3A0x747fd5298859a7a7!2sSaint%20Monique%20Valais!5e0!3m2!1sen!2suk!4v1631086053421!5m2!1sen!2suk" width="850" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

            </div>
        </div>
    </footer>

    <div class="footer">
        <p>© 2024 St. Monique Valais Homeowners Association. All rights reserved.</p>
    </div>
    <script src="HomepageJS.js"></script>
</body>

</html>

</body>
</html>
