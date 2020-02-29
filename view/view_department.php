<?php include "header.php"?>
<?php include "sidebar.php"?>
<?php
use App\Session\Session;
use App\Department\Department;
$department = new Department();
?>
<?php
$errors = array();
$code = "";
$name = "";
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border text-center">
                <h1 class="box-title">Department Lists</h1>
            </div>

            <div class="box-body">
                <div class="col-sm-offset-5">
                    <?php
                    echo Session::SuccessMsg();
                    echo Session::ErrorMsg();
                    ?>
                </div>
                <div>
                    <ul class="nav nav-tabs">
                        <?php
                        $path = $_SERVER["SCRIPT_FILENAME"];
                        $CurreentPage = basename($path, '.php');
                        ?>
                        <li class="active" role="presentation"><a href="view_department.php">Running Departments</a></li>
                        <li role="presentation"><a href="trash-list.php">Trash</a></li>
                    </ul>
                </div>
                <br>
                <div class="table-responsive col-md-12">
                    <?php
                    if (isset($_GET["trashId"])) {
                        $trashId = $_GET["trashId"];
                        $department->moveTo_trash($trashId);
                    }
                    ?>

                    <table id="table_info" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Edit</th>
                            <th>Move Trash</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach (Department::getAllDepartment() as $key => $value){
                            $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $value["code"]; ?></td>
                            <td><?php echo $value["title"]; ?></td>
                            <td><a class="btn btn-default" href="edit-department.php?editId=<?php echo $value["id"]; ?>">Edit</a></td>
                            <td><a class="btn btn-default" onclick="return confirm('Are u sure to move?')" href="?trashId=<?php echo $value["id"]; ?>">Move To
                                    Trash</a></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<script>
    $(document).ready(function(){
        $('#table_info').DataTable();
    });
</script>
<?php include "footer.php"?>

