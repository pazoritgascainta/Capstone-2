<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="HomepageStyle.css">


    <title>St. Monique</title>
</head>

<body>
    <div class="logo">
        <p>St. Monique Valais Homeowners' Association</p>
    </div>
    <div class="logoo">
    <a href="#"><img src="https://scontent.fmnl25-3.fna.fbcdn.net/v/t1.15752-9/436198750_975421137542976_1645205689955435346_n.png?_nc_cat=101&ccb=1-7&_nc_sid=5f2048&_nc_eui2=AeEL8Vkpr0Jpsu0g8wLT7ALd6j3Doeu5tmrqPcOh67m2aoZg0c9FYmQJIa26wea7OGkAC0afXjvYpXq53cjGWoqM&_nc_ohc=-6DUKtCORYMQ7kNvgHC5smh&_nc_ht=scontent.fmnl25-3.fna&oh=03_Q7cD1QGtM29RYwFCMKEX1SE7-r4hzlCW-JU83jcL7lBkaQ-wzQ&oe=66750BD9" alt="Your Logo"></a>
    </div>
    <header>
        <nav>
            <ul>
                <!-- Updated navigation items -->
                <li><a href="Homepage.php">Home</a></li>
                <li><a href="#" id="loginBtn">Login</a></li>
                <li><a href="ContacUS.php">Contact</a></li>
                <li><a href="Homepage.php">Amenities</a></li>
                <!-- New login button -->
                <li class="dropdown">
                <a href="#" class="dropbtn"></a>
                <div>
                    <button id="modeToggle">Toggle Dark Mode</button>
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
<div class="ImageSlider">
    <input type="radio" id="trigger1" name="slider"checked autofocus>
    <label for="trigger1"><span class="sr-only"></span></label>
    <div class="slide bg1">
        <div class="description">
            <h3>St Monique Main Building</h3>
        </div>
    </div>
    
    <input type="radio" id="trigger2" name="slider" checked autofocus>
    <label for="trigger2"><span class="sr-only"></span></label>
    <div class="slide bg2">
        <div class="description">
            <h3>Wide Parking Slots</h3>
        </div>
    </div>
    
    <input type="radio" id="trigger3" name="slider" checked autofocus>
    <label for="trigger3"><span class="sr-only"></span></label>
    <div class="slide bg3">
        <div class="description">
            <h3>St Monique Chapel</h3>
        </div>
    </div>
    
    <input type="radio" id="trigger4" name="slider" checked autofocus>
    <label for="trigger4"><span class="sr-only"></span></label>
    <div class="slide bg4">
        <div class="description">
            <h3>Clubhouse</h3>
        </div>
    </div>
    
    <input type="radio" id="trigger5" name="slider" checked autofocus>
    <label for="trigger5"><span class="sr-only"></span></label>
    <div class="slide bg5">
        <div class="description">
            <h3>St Monique Valais</h3>
        </div>
    </div>
</div>
     <!-- Images Highlights -->
    <section>
        <div class="Amenities">
            <h2>St Monique Valais' Amenities</h2>
            <ul>
                <li>
                    <img src="https://scontent.fmnl25-1.fna.fbcdn.net/v/t1.15752-9/432737758_1123670605645215_6395987856012548444_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=5f2048&_nc_ohc=-vg_A8E0724Q7kNvgHfHBl6&_nc_ht=scontent.fmnl25-1.fna&oh=03_Q7cD1QE8mObTGrxqAwOx1qDKxfryDAQPbpjugHVJ66q_3r3i0A&oe=6666B752" alt="Swimming Pool">
                    <div class="text">
                        <h3>Swimming Pool</h3>
                    </div>
                </li>
                <li><img src="https://scontent.fmnl25-1.fna.fbcdn.net/v/t1.15752-9/433447835_837376578223957_712533863247073136_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=5f2048&_nc_ohc=X_azpxCDfSwQ7kNvgHOHlZL&_nc_ht=scontent.fmnl25-1.fna&oh=03_Q7cD1QGZ4k3BQK89ArK9TaoTysDsyap3gkwC3LQhYTnUjkTpGA&oe=6666CEEE" alt="Basketball Court">
                    
                    <div class="text">
                        <h3>Basketball Court</h3>
                    </div>
                </li>
                <li><img src="https://scontent.fmnl34-1.fna.fbcdn.net/v/t39.30808-6/337172853_550043030530126_2270268773605201229_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=5f2048&_nc_eui2=AeFLKqhHDJ8z51Z6He4Tup9hwyyll8Trx4vDLKWXxOvHi520vmFq06l-j66C5zCuNQEAkDi1jnVxvpzx4dmYBAi8&_nc_ohc=9rEAFmGzTvoQ7kNvgEmH7K-&_nc_zt=23&_nc_ht=scontent.fmnl34-1.fna&oh=00_AYDrvbEHn5saQoM5ofV2WWAoPHg-UqlY_WrYTHN_4mfGzw&oe=664FFD00" alt="Playground">
                    <div class="text">
                        <h3>Playground</h3>
                    </div>
                </li>
                <li><img src="https://scontent.fmnl25-1.fna.fbcdn.net/v/t1.15752-9/431354655_733075685644924_3848879267469360538_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=5f2048&_nc_ohc=dExEOMrmAJ4Q7kNvgFz3TIK&_nc_ht=scontent.fmnl25-1.fna&oh=03_Q7cD1QGPyT_D4nSyAERctVYJ7wnCUWXLPYjP8UmRzRXZX2IOJA&oe=6666C4C4" alt="Swimming Pool">
                    <div class="text">
                        <h3>Clubhouse</h3>
                    </div>
                </li>                            
            </ul>
        </div>
    </section>
    <h1 style="text-align: center;">St.Monique Valais HOA Drone Fly Over</h1>
<div class="video-container">
<video autoplay muted controls>
    <source src="St. Monique Valais.mp4" type="video/mp4">
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
                    <div class="carousel-item active">
                        <img src="https://thumbs.dreamstime.com/b/under-maintenance-detailed-illustration-grungy-maintanance-construction-background-61865170.jpg" alt="Announcement Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="https://thumbs.dreamstime.com/b/under-maintenance-detailed-illustration-grungy-maintanance-construction-background-61865170.jpg" alt="Announcement Image 3">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#" role="button">
                    <span class="carousel-control-prev-icon" aria-hidden="true">&lt;</span>
                </a>
                <a class="carousel-control-next" href="#" role="button">
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
                <img src="https://scontent.fmnl25-3.fna.fbcdn.net/v/t1.15752-9/436198750_975421137542976_1645205689955435346_n.png?_nc_cat=101&ccb=1-7&_nc_sid=5f2048&_nc_eui2=AeEL8Vkpr0Jpsu0g8wLT7ALd6j3Doeu5tmrqPcOh67m2aoZg0c9FYmQJIa26wea7OGkAC0afXjvYpXq53cjGWoqM&_nc_ohc=-6DUKtCORYMQ7kNvgHC5smh&_nc_ht=scontent.fmnl25-3.fna&oh=03_Q7cD1QGtM29RYwFCMKEX1SE7-r4hzlCW-JU83jcL7lBkaQ-wzQ&oe=66750BD9" width="550" height="250" alt="St Monique Valais Logo">
                <h2>St Monique Valais Homeowners Association</h2>
                <p>Welcome to St. Monique Valais, a beacon of modern living nestled in the heart of our region.
                    Established in 2005 by a visionary in the real estate industry, our community stands as a testament to meticulous planning, upscale amenities, and well-designed homes.
                     Our residents don’t just live here they actively participate in shaping the community through decision-making processes and engaging activities.
                     Experience the hallmark of modern living at St. Monique Valais!.</p>
            <ul>
            <h2>Contact Us</h2>
                <li><i class="fa fa-envelope"></i> Email: example@example.com</li>
                <li><i class="fa fa-phone"></i> Phone: +1234567890</li>
            </ul>
            <h2>Follow Us</h2>
            <ul class="social-links">
                <li><a href="https://www.facebook.com/SMVRizal"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
            </ul>
        </div>
            <div class="location-map">
                <h2>Our Location</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.2986102152097!2d121.18476209999999!3d14.509480000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c16a37b4ca23%3A0x747fd5298859a7a7!2sSaint%20Monique%20Valais!5e0!3m2!1sen!2suk!4v1631086053421!5m2!1sen!2suk" width="850" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

            </div>
        </div>
    </footer>

    <div class="footer">
        <p>© 2024 St. Monique Valais Homeowners' Association. All rights reserved.</p>
    </div>
    <script src="HomepageJS.js"></script>
</body>

</html>

</body>
</html>
