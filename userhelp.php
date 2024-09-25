<?php
session_name('user_session'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="userhelp.css">


</head>
<body>

        <?php include 'usersidebar.php'; ?>
        <div class="main-content">
            <h1></h1>
<div>
  <!-- dito ilagay ang contents -->
            <div class="container">   
              <h1>USER HELP</h1>

  <section class="main-section">
    <div class="help-center">
      <div class="topics">
        <div class="topic" onclick="showFAQ('Account')">St.Monique Account FAQ</div>
        <div class="topic" onclick="showFAQ('complaints')">FAQ on Complaints Module</div>
        <div class="topic" onclick="showFAQ('payment')">FAQ on on Payment Module</div>
        <div class="topic" onclick="showFAQ('Appointment')">FAQ on Appointment Module</div>
        <div class="topic" onclick="showFAQ('Request')">FAQ on Service Request Module</div>
      </div>
      <div class="faq-content">
    <div id="Account" class="faq" style="display:none;">
        <h3>St. Monique Account FAQ</h3>
        <p><strong>Q: Can I create another account?</strong><br>
        A: No, each Homeowners have only one (1) Account and the admin of St.Monique Valais only have the right to create a account for St.Monique Homeowners.</p>
        <p><strong>Q: I forgot my password. What should I do?</strong><br>
        A: Option 1: Click on the "Forgot Password?" link on the login module on homepage and follow the instructions to reset your password.</p>     
        <p>A: Option 2: Go onto your settings under your profile and you can reset your password there.</p>
        <p><strong>Q: How can I update my account information?</strong><br>
        A: Log in to your account and navigate to the "Profile" section where you can update your personal information.</p>
    </div>
    <div id="complaints" class="faq" style="display:none;">
        <h3>FAQ on Complaints Module</h3>
        <p><strong>Q: How do I submit a complaint?</strong><br>
        A: To submit a complaint, visit the "Complaints" section and fill out the form with details of your issue.</p>
        <p><strong>Q: What is the response time for complaints?</strong><br>
        A: We aim to respond to all complaints within 48 hours. You will receive a notification once your complaint is reviewed.</p>
        <p><strong>Q: Can I track the status of my complaint?</strong><br>
        A: Yes, you can track the status of your complaint in the "View Your Complaints" section of your account.</p>
    </div>
    <div id="payment" class="faq" style="display:none;">
        <h3>FAQ on Payment Module</h3>
        <p><strong>Q: What payment methods are accepted?</strong><br>
        A: St.Monique only accepts Cash (Pay directly on our clubhouse) and Gcash payments, you need to upload the image file of proof of transaction on your "Payment" module and we will review and confirm your payment on your monthly due.</p>
        <p><strong>Q: What Happen when i missed a payment for a month?</strong><br>
        A: Your Monthly due on the next billing will be stacked with maximum of 2 months only.</p>
        <p><strong>Q: Where i can see my Billing Statement for the month?</strong><br>
        A: Your Billing Statement can be seen also inside the Payment Module.</p>
    </div>
    <div id="Appointment" class="faq" style="display:none;">
        <h3>FAQ on Appointment Module</h3>
        <p><strong>Q: How do I schedule an appointment?</strong><br>
        A: You can schedule an appointment of any Amenities by navigating to the "Appointments" module  and you will see a Calendar and select your preferred date and time.</p>
        <p><strong>Q: Can I reschedule my appointment?</strong><br>
        A: Yes, you can reschedule your appointment by going to the "My Appointments" section and selecting the reschedule option.</p>
        <p><strong>Q: What should I do if I need to cancel my appointment?</strong><br>
        A: If you need to cancel, please do so at least 24 hours in advance through the "Appointments" module and see your "Pending Appointments" and on the Action click "Cancel".</p>
    </div>
    <div id="Request" class="faq" style="display:none;">
        <h3>FAQ on Service Request Module</h3>
        <p><strong>Q: How do I submit a service request?</strong><br>
        A: To submit a service request, go to the "Service Requests" section and fill out the form with your needs.</p>
        <p><strong>Q: What types of services can I request?</strong><br>
        A: You can request a variety of services, including technical support, maintenance, and general inquiries.</p>
        <p><strong>Q: How long does it take to process a service request?</strong><br>
        A: We aim to process all service requests within 72 hours and will notify you once your request is complete.</p>
    </div>
</div>
    </div>
  </section>
</div>
</div>
</div>
<script src="userhelp.js"></script>

</body>
</html>
