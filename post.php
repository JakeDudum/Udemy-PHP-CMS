<?php ob_start(); ?>
<?php include "includes/db.php" ?>
<?php include "includes/header.php" ?>

<!-- Navigation -->
<?php include "includes/navigation.php" ?>

<?php

if (isset($_POST['liked'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    $query = "SELECT * FROM posts WHERE post_id = $post_id";
    $postResult = mysqli_query($connection, $query);
    $post = mysqli_fetch_array($postResult);
    $likes = $post['likes'];

    mysqli_query($connection, "UPDATE posts SET likes = $likes+1 WHERE post_id = $post_id");

    mysqli_query($connection, "INSERT INTO likes(user_id, post_id) VALUES($user_id, $post_id)");
    exit();
}

if (isset($_POST['unliked'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    $query = "SELECT * FROM posts WHERE post_id = $post_id";
    $postResult = mysqli_query($connection, $query);
    $post = mysqli_fetch_array($postResult);
    $likes = $post['likes'];

    mysqli_query($connection, "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id");

    mysqli_query($connection, "UPDATE posts SET likes = $likes-1 WHERE post_id = $post_id");

    exit();
}

?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php
            if (session_status() == PHP_SESSION_NONE) session_start();

            if (isset($_GET['p_id'])) {
                $the_post_id = $_GET['p_id'];

                $view_query = "UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = $the_post_id";
                $send_query = mysqli_query($connection, $view_query);

                if (!$send_query) {
                    die("Query Failed");
                }

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                    $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} ";
                } else {
                    $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} AND post_status = 'published'";
                }

                $select_all_posts_query = mysqli_query($connection, $query);

                if (mysqli_num_rows($select_all_posts_query) < 1) {
                    echo "<h1 class='text-center'>No Posts Available</h1>";
                } else {

                    while ($row = mysqli_fetch_assoc($select_all_posts_query)) {
                        $post_title = $row['post_title'];
                        $post_user = $row['post_user'];
                        $post_date = $row['post_date'];
                        $post_image = $row['post_image'];
                        $post_content = $row['post_content'];

            ?>

                        <h1 class="page-header">Posts</h1>

                        <!-- First Blog Post -->
                        <h2>
                            <a href="#"><?php echo $post_title ?></a>
                        </h2>
                        <p class="lead">
                            by <a href="author_posts-<?php echo $post_user ?>-<?php echo $the_post_id ?>"><?php echo $post_user ?></a>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span><?php echo $post_date ?></p>
                        <hr>
                        <img class="img-responsive" src="images/<?php echo imagePlaceholder($post_image) ?>" alt="">
                        <hr>
                        <p><?php echo $post_content ?></p>

                        <?php if (isLoggedIn()) : ?>

                            <hr>
                            <div class="row">
                                <p class="pull-right"><a class="<?php echo userLikedThisPost($the_post_id) ? 'unlike' : 'like' ?>" href="">
                                        <span class="glyphicon glyphicon-thumbs-<?php echo userLikedThisPost($the_post_id) ? 'down' : 'up' ?>" data-toggle="tooltip" data-placement="top" title="<?php echo userLikedThisPost($the_post_id) ? 'I liked this before' : 'Want to Like it?' ?>"></span>
                                        <?php echo userLikedThisPost($the_post_id) ? ' Unlike' : ' Like' ?></a></p>
                            </div>

                        <?php else : ?>

                            <div class="row">
                                <p class="pull-right">You need to <a href="login">Login</a> to like</p>
                            </div>

                        <?php endif; ?>

                        <div class="row">
                            <p class="pull-right">Likes: <?php echo getPostLikes($the_post_id); ?></p>
                        </div>
                        <div class="clearfix"></div>

                    <?php } ?>

                    <!-- Blog Comments -->

                    <?php

                    if (isset($_POST['create_comment'])) {
                        $the_post_id = $_GET['p_id'];

                        $comment_author = $_SESSION['username'];
                        $comment_user_id = $_SESSION['user_id'];
                        $comment_email = $_SESSION['user_email'];
                        $comment_content = $_POST['comment_content'];

                        if (!empty($comment_content)) {

                            $query = "INSERT INTO comments (comment_post_id, comment_user_id, comment_author, comment_email, comment_content, comment_status, comment_date) ";
                            $query .= "VALUES ($the_post_id, $comment_user_id, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unapproved', now() ) ";

                            $create_comment_query = mysqli_query($connection, $query);

                            if (!$create_comment_query) {
                                die('QUERY FAILED' . mysqli_error($connection));
                            }
                        } else {
                            echo "<script>alert('Fields cannot be empty')</script>";
                        }
                    }

                    ?>

                    <?php if (isLoggedIn()) : ?>

                        <!-- Comments Form -->
                        <div class="well">
                            <h4>Leave a Comment:</h4>
                            <form action="" method="post" role="form">
                                <div class="form-group">
                                    <label for="Comment">Your Comment</label>
                                    <textarea name="comment_content" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
                            </form>
                        </div>

                        <hr>
                    <?php else : ?>

                        <div class="well">
                            <h4>Please <a href="login">Login</a> to leave a comment</h4>
                        </div>

                    <?php endif; ?>

                    <!-- Posted Comments -->

                    <?php

                    $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
                    $query .= "AND comment_status = 'approved' ";
                    $query .= "ORDER BY comment_id DESC ";
                    $select_comment_query = mysqli_query($connection, $query);

                    if (!$select_comment_query) {
                        die('QUERY FAILED' . mysqli_error($connection));
                    }

                    while ($row = mysqli_fetch_assoc($select_comment_query)) {
                        $comment_date = $row['comment_date'];
                        $comment_content = $row['comment_content'];
                        $comment_author = $row['comment_author'];
                    ?>
                        <!-- Comment -->
                        <div class="media">
                            <a class="pull-left" href="#">
                                <img class="media-object" src="http://placehold.it/64x64" alt="">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><?php echo $comment_author; ?>
                                    <small><?php echo $comment_date; ?></small>
                                </h4>
                                <?php echo $comment_content; ?>
                            </div>
                        </div>

            <?php }
                }
            } else {
                header("Location: index.php");
            } ?>
        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php" ?>

    </div>
<!-- /.row -->

<hr>

<!-- Footer -->
<?php include "includes/footer.php" ?>

<script>
    $(document).ready(function() {

        $("[data-toggle='tooltip']").tooltip();

        let post_id = <?php echo $the_post_id; ?>;
        let user_id = <?php echo loggedInUserId(); ?>;

        $('.like').click(function() {
            $.ajax({
                url: "post-" + post_id,
                type: 'post',
                data: {
                    'liked': 1,
                    'post_id': post_id,
                    'user_id': user_id
                }
            })
        })

        $('.unlike').click(function() {
            $.ajax({
                url: "post-" + post_id,
                type: 'post',
                data: {
                    'unliked': 1,
                    'post_id': post_id,
                    'user_id': user_id
                }
            })
        })

    });
</script>