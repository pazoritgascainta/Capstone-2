
    <link rel="stylesheet" href="navbaradmin.css">

<body>
    <div class="headnavbar">
    <nav>
    <img src="monique logo.png" width="170" height="80"alt="logo" >
        <ul>
            <li><a href='#'>Home</a> </li>
            <li><a href='#'>Notifications</a> </li>
        </ul>
        <img src="profile.png" class="user-pic" onclick="toggleMenu()">
        <div class = "sub-menu-wrap" id="subMenu">
            <div class = "sub-menu">
                <div class ="user-info">
                    <img src="profile.png" >
                    <H2>John Doe</H2>
                </div>
                <hr>
                <a href ="#" class= "sub-menu-link">
                    <img src="account.png" alt="">
                    <p>Edit Profile</p>
                    <span>></span>
                </a>
                <a href ="#" class= "sub-menu-link">
                    <img src="settings.png" alt="">
                    <p>Settings</p>
                    <span>></span>
                </a>
                <a href ="#" class= "sub-menu-link">
                    <img src="help.png" alt="">
                    <p>Help</p>
                    <span>></span>
                </a>
                <a href ="#" class= "sub-menu-link">
                    <img src="logawt.png" alt="">
                    <p>Logout</p>
                    <span>></span>
                </a>
                </div>
                </div>
            </nav>
            
</div>
<script>
    let subMenu = document.getElementById("subMenu");

    function toggleMenu(){
        subMenu.classList.toggle("open-menu");
    }
</script>
</body>
