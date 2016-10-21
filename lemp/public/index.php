<?php

$rows = array();
    
// Try and connect to the database
$connection = mysqli_connect("10.0.75.2:3306", "root", "admin", "docker_sample");

// If connection was not successful, handle the error
if($connection === false) {
    echo "Unable to connect to mysql.";
}
else {
    $result = mysqli_query($connection,"select * from users");
    
    // Fetch all the rows in an array
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>PHP Docker Sample</title>
        <meta charset="utf-8">
    </head>
    <body>

        <?php if (count($rows) == 0) { ?>
            <h1>No user has been registered yet.</h1>
        <?php } else { ?>

            <table border="1">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            
            <?php foreach ($rows as &$row) { ?>

                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                </tr>
            
            <? } ?>
            </table>

        <?php }?>
    </body>
</html>