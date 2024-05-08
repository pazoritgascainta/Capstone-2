<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="billcss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>St. Monique Billing </h1>
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