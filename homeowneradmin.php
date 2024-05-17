<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeowners</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="homeownercss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="Container">
            <div class="container">
                <h2>List of Homeowners</h2>
                <a class="btn btn-primary" href="create.php" role="button">New Homeowner</a>
                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>    
                            <th>Phone</th>    
                            <th>Address</th>    
                            <th>Created At</th>  
                            <th>Action</th>      
                            <th></th>    
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $database = "homeowner";

                        $conn = new mysqli($servername, $username, $password, $database);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "SELECT * FROM homeowners";
                        $result = $conn->query($sql);

                        if (!$result){
                            die("Invalid query" . $conn->error);       
                        }

                        while($row = $result->fetch_assoc()){
                            echo "
                            <tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['phone_number']}</td>
                                <td>{$row['address']}</td>
                                <td>{$row['created_at']}</td>
                                <td>
                                 <a class='btn btn-primary btn-sm' href='edit.php?id={$row['id']}'> Edit</a>
                                    <a class='btn btn-primary btn-sm delete-btn' data-id='{$row['id']}'> Delete</button>
                                
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You won\'t be able to revert this!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `delete.php?id=${id}`;
                        }
                    });
                });
            });
        });
    </script>
    <script>
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };
</script>

</body>
</html>
