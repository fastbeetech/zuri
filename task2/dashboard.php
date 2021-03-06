<?php 
    require_once 'layouts/header.php';
    require 'extra/checker.php';
    require_once 'layouts/nav.php';
    require_once 'config.php';
?>
<div class="container">
        <h2 class="text-center alert alert-light">Welcome <?= $session_user ?> to your dashboard</h2>
        <div style="margin-bottom: 40px;"></div>
        <?php require_once 'extra/messages.php' ?>
        <div class="row justify-content-center">

            <form action="course.php" method="post">                          

                <?php if(isset($_GET['add_course'])) : ?>
                <h5>Add Course</h5>
                
                <!-- session id -->
                <input type="hidden" name="user_id" value="<?= $session_id ?>">

                <div class="form-group">
                    <label for="">Title</label>
                    <input type="text" name="title" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Code</label>
                    <input type="text" name="code" class="form-control">
                </div>
                
                <div class="form-group">
                    <input type="submit" name="add" class="btn btn-primary" value="Add Course">
                </div>
            </form>

        <?php elseif(isset($_GET['edit'])): 
            include_once 'edit_course.php';
        ?>
    </div>
</div>
            <!-- result table -->
        <?php else: ?>
        <div class="table-responsive w-75 m-auto">
                
                <?php
                    $sql = "SELECT * FROM courses WHERE user_id=?";
                    // echo $session_id;
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("i", $session_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    if($data) :
                ?>
                
                <table class="table table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Course Code</th>
                            <th>Registered Date</th>
                            <th colspan="2">Action</th>
                        </tr>
                    </thead>
                    <?php
                        $sql = "SELECT * FROM courses WHERE user_id=?";
                        // echo $session_id;
                        $stmt = $conn->prepare($sql); 
                        $stmt->bind_param("i", $session_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $data = $result->fetch_all(MYSQLI_ASSOC);
                        if($data) :
                        foreach($data as $row):
                    ?>
                    
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['code']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a href="dashboard.php?edit=<?= $row['id'] ?>" class="btn btn-primary">Edit</a>
                            </td>
                            <td>
                                <a href="course.php?delete=<?= $row['id'] ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach;  else: ?>
                        <div class="alert alert-light">No data found</div>
                    <?php endif; ?>
                </table>
                    <?php endif; ?>              
       </div>
<?php endif; ?>
