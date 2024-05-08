<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report</title>
    <link rel="stylesheet" href="dashbcss.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="Container">
        <h1>St. Monique Report</h1>
    </div>
</div>

</body>
<script>
let btn = document.querySelector('#btn')
let sidebar = document.querySelector('.sidebar')

btn.onclick = function () {
    sidebar.classList.toggle('active');
}
</script>



</html>