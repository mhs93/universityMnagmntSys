<?php include "header.php"?>
<?php include "sidebar.php"?>
<?php
use App\Database\Database;
use App\Utility\Utility;
use App\Session\Session;
use App\Student\Student;
use App\Result\Result;
use App\Course\Course;
$course = new Course();
$student = new Student();
$result = new Result();
?>
<?php
$errors = array();
$name = "";
$email = "";
?>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border text-center">
                <h1 class="box-title">Save Student Result</h1>
            </div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["submit"])) {
                    if (empty($_POST["courseId"])){
                        $errors['courseEmpty'] = "Please select a course.";
                    }
                    if (!empty($_POST["courseId"])) {
                        $regNo = trim($_POST["regNo"]);

                        $course = "SELECT results.*, students.reg_no FROM results LEFT JOIN students ON results.reg_id = students.id WHERE reg_id = 
'$regNo' LIMIT 1";
                        $courseCheck = Database::Prepare($course);
                        $courseCheck->execute();
                        $courseFound = $courseCheck->fetch();
                        if ($courseFound["reg_id"] == $regNo && $courseFound["course_id"] == $_POST["courseId"]) {
                            $errors['courseMatch'] = "This course's result already saved for " . $courseFound["reg_no"];
                        }
                    }
                    if (empty($_POST["grade"])){
                        $errors['gradeEmpty'] = "Please select a grade letter.";
                    }
                    if (empty($errors)){
                        $result->saveResult($_POST);
                    }
                }
            }
            ?>
            <?Php
            if (isset($_POST["back"])){
                echo "<script>window.location = 'view-result.php'</script>";
            }
            ?>
            <div class="box-body">
                <?php
                if (!isset($_GET["resultId"])){
                    header("Location: result.php");
                }
                else{
                    $id = $_GET["resultId"];
                    $data = $student->getStudentsById($id);
                    ?>
                    <form action="" method="post" class="form-horizental col-sm-offset-4 col-sm-4 col-sm-offset-4">
                        <?php
                        echo Session::SuccessMsg();
                        echo Session::ErrorMsg();
                        ?>
                        <div class="form-group">
                            <label for="">Student Reg. No.</label>
                            <input type="text" class="form-control" readonly value="<?php echo
                            $data['reg_no']; ?>">
                            <input type="hidden" name="regNo" value="<?php echo $data['id']; ?>">
                        </div>
                        <div id="std_detail" class="form-group">
                            <label>Student Name</label>
                            <input type="text" name="name" class="form-control" readonly value="<?php echo
                            $data['title']; ?>">
                            <br>
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" readonly value="<?php echo
                            $data['email']; ?>">
                            <br>
                            <label for="">Department</label>
                            <input type="text" name="department" class="form-control" readonly value="<?php echo
                            $data['code']; ?>">
                            <br>
                            <label for="">Courses</label>
                            <select id="courseId" name="courseId" class="form-control selectpicker" data-show-subtext="true" data-live-search="true">
                                <option value="">&larr; Select Course &rarr;</option>
                                <?php
                                $query = "SELECT entrol_course.course_id, courses.course_name FROM entrol_course LEFT JOIN courses 
ON entrol_course.course_id=courses.id WHERE entrol_course.reg_no = '" .$_GET["resultId"]."'";
                                $stmt = Database::Prepare($query);
                                $stmt->execute();
                                $data =  $stmt->fetchAll();

                                foreach ($data as $courses){ ?>
                                    <option value="<?php echo $courses['course_id']; ?>"><?php echo $courses['course_name']; ?></option>
                                <?php } ?>
                            </select>
                            <?php if (!empty($errors['courseEmpty'])){
                                echo Utility::error($errors["courseEmpty"]);
                            } ?>
                            <?php if (!empty($errors['courseMatch'])){
                                echo Utility::error($errors["courseMatch"]);
                            } ?>

                        </div>
                        <div class="form-group">
                            <label for="">Select Grade Letter</label>
                            <select name="grade" class="form-control selectpicker" data-show-subtext="true" data-live-search="true">
                                <option value="">&larr; Select Grade &rarr;</option>
                                <?php foreach (Result::getAll_grades() as $grade){ ?>
                                    <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade']; ?></option>
                                <?php } ?>
                            </select>
                            <?php if (!empty($errors['gradeEmpty'])){
                                echo Utility::error($errors["gradeEmpty"]);
                            } ?>
                        </div>
                        <button type="submit" name="submit" class="btn btn-info">Save</button>
                        <button type="submit" name="back" class="btn btn-info">Back</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<script>
    $('#date').datepicker({ dateFormat:'yy-mm-dd' });
</script>
<script>
    $(document).ready(function () {
        $('#regNo').change(function () {
            var studentId = $(this).val();
            $.ajax({
                url:"loader.php",
                method:"POST",
                data:{studentId:studentId},
                dataType:"text",
                success:function (data) {
                    $('#std_detail').html(data);
                }
            });
        });
    });
</script>
<?php include "footer.php"?>

