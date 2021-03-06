<?php

if (isset($_GET['edit_user'])) {

    $the_user_id = $_GET['edit_user'];

    $query = "SELECT * FROM users WHERE user_id = {$the_user_id}";
    $select_users_query = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc(($select_users_query))) {
        $user_id = $row['user_id'];
        $user_firstname = $row['user_firstname'];
        $user_lastname = $row['user_lastname'];
        $user_role = $row['user_role'];
        $username = $row['username'];
        $user_email = $row['user_email'];
        $user_password = $row['user_password'];
    }

    if (isset(escape($_POST['update_user']))) {
        $user_firstname = escape($_POST['user_firstname']);
        $user_lastname = escape($_POST['user_lastname']);
        $user_role = escape($_POST['user_role']);
        $username = escape($_POST['username']);
        $user_email = escape($_POST['user_email']);
        $user_password = escape($_POST['user_password']);

        if (empty($user_password)) {

            $query_password = "SELECT user_password FROM users WHERE user_id = $the_user_id";
            $get_user_query = mysqli_query($connection, $query_password);
            confirm($get_user_query);

            $row = mysqli_fetch_array($get_user_query);

            $hashed_password = $row['user_password'];

        } else {
            $hashed_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 12));;
        }


        $query = "UPDATE users SET ";
        $query .= "user_firstname = '{$user_firstname}', ";
        $query .= "user_lastname = '{$user_lastname}', ";
        $query .= "user_role = '{$user_role}', ";
        $query .= "username = '{$username}', ";
        $query .= "user_email = '{$user_email}', ";
        $query .= "user_password = '{$hashed_password}' ";
        $query .= "WHERE user_id = {$the_user_id} ";

        $edit_user_query = mysqli_query($connection, $query);
        confirm($edit_user_query);

        echo "User Updated" . " <a href='users.php'>View Users</a>";
    }
} else {
    header("Location: index.php");
}

?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="author">Firstname</label>
        <input type="text" value='<?php echo $user_firstname; ?>' class="form-control" name="user_firstname">
    </div>
    <div class="form-group">
        <label for="post_status">Lastname</label>
        <input type="text" value='<?php echo $user_lastname; ?>' class="form-control" name="user_lastname">
    </div>
    <select name="user_role" id="user_role">
        <option value=<?php echo $user_role; ?>><?php echo $user_role; ?></option>
        <?php

        if ($user_role == 'admin') {
            echo "<option value='subscriber'>subscriber</option>";
        } else {
            echo "<option value='admin'>admin</option>";
        }

        ?>
    </select>
    <!-- <div class="form-group">
        <label for="post_image">Post Image</label>
        <input type="file" name="post_image">
    </div> -->
    <div class="form-group">
        <label for="post_tags">Username</label>
        <input type="text" value=<?php echo $username; ?> class="form-control" name="username">
    </div>
    <div class="form-group">
        <label for="post_content">Email</label>
        <input type="email" value=<?php echo $user_email; ?> class="form-control" name="user_email">
    </div>
    <div class="form-group">
        <label for="post_content">Password</label>
        <input autocomplete="off" placeholder="Leave Blank to keep Current Password" type="password" class="form-control" name="user_password">
    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="update_user" value="Update User">
    </div>
</form>