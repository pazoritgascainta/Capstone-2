<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<title>Contact Us</title>
<link rel="stylesheet" href="ContactUSStyle.css">
<link rel="stylesheet" href="HomepageLogo.css">
<link rel="stylesheet" href="HomepageHeader.css">

</head>
<body>

    <section class = "HomeLogo">
    <div class="logoo">
    <a href="Homepage.php"><img src="monique logo.png" alt="Monique Logo"></a>
    </div>
    </section>
</header>
<!-- Contact Us Section -->
<div class="container">
	<div class="text">
	   Contact us Form
	</div>
<form action="https://api.web3forms.com/submit" method="POST">
    <input type="hidden" name="access_key" value="a4811e7a-1c82-4b53-b502-d3ef216b1481">

    <div class="form-row">
        <div class="input-data">
            <input type="text" name="first_name" required>
            <div class="underline"></div>
            <label for="first_name">First Name</label>
        </div>
        <div class="input-data">
            <input type="text" name="last_name" required>
            <div class="underline"></div>
            <label for="last_name">Last Name</label>
        </div>
    </div>
    
    <div class="form-row">
        <div class="input-data">
            <input type="email" name="email" required>
            <div class="underline"></div>
            <label for="email">Email Address</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data textarea">
            <textarea name="message" rows="8" cols="80" required></textarea>
            <div class="underline"></div>
            <label for="message">Write your message</label>
        </div>
    </div>

    <div class="form-row submit-btn">
        <div class="input-data">
		   <div class="inner"></div>
            <button type="submit">Submit</button>
        </div>
    </div>
</form>

	</div>
</body>
</html>
