<?php include "header.php"?>
<?php include "sidebar.php"?>
<?php
use App\Session\Session;
use App\Department\Department;
use App\Student\Student;
$department = new Department();
$student = new Student();
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
                <h1 class="box-title">Students List</h1>
            </div>
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
                    <li class="active" role="presentation"><a href="student-list.php">Active Students</a></li>
                    <li role="presentation"><a href="students-trash-list.php">Trash</a></li>
                </ul>
            </div>
            <br>
            <div class="box-body">
                <div class="col-sm-offset-4 col-sm-4 col-sm-offset-4 text-center">
                    <label for="">Select Department</label>
                    <select name="depart" id="depart" class="form-control text-center">
                        <option value="">Show All Students</option>
                        <?php foreach (Department::getAllDepartment() as $value) { ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="table-responsive col-md-12">
                    <br>
                    <?php
                    if (isset($_GET["trashId"])) {
                        $trashId = $_GET["trashId"];
                        $student->moveTo_trash($trashId);
                    }
                    ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Reg. No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Reg. Date</th>
                            <th>Department</th>
                            <th>View</th>
                            <th>Edit</th>
                            <th>Trash</th>
                        </tr>
                        </thead>
                        <tbody class="showStudents">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<script>
    $(document).ready(function () {
        $('#depart').change(function () {
            var depart_id = $(this).val();
            $.ajax({
                url:"loader.php",
                method:"POST",
                data:{depart_id:depart_id},
                success:function (data) {
                    $('.showStudents').html(data);
                }
            });
        });
    });
</script>
<?php include "footer.php"?>
