<?php include "header.php"?>
<?php include "sidebar.php"?>
<?php
use App\Department\Department;
use App\Database\Database;
use App\Session\Session;
use App\Course\Course;
use App\AllocateClass\AllocateClass;
use App\Utility\Utility;
$allocate = new AllocateClass();
$course = new Course();
?>
<?php
$errors = array();
$start= "";
$end = "";
?>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border text-center">
                <h1 class="box-title">Update Schedule</h1>
            </div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["submit"])) {
                    $start = $_POST["start"];
                    $end = $_POST["end"];

                    $roomId = $_POST["roomId"];
                    $dayId = $_POST["dayId"];
                    $startLen = strlen($_POST["start"] );
                    $endLen = strlen($_POST["end"] );
                    if (!is_numeric($_POST["deptId"])) {
                        $errors['deptEmpty'] = "Please select a department!";
                    }
                    if (!is_numeric($_POST["courseId"])) {
                        $errors['courseEmpty'] = "Please select a course!";
                    }
                    if (!is_numeric($_POST["roomId"])) {
                        $errors['roomEmpty'] = "Please select a room!";
                    }
                    if (!is_numeric($_POST["dayId"])) {
                        $errors['dayEmpty'] = "Please select a day!";
                    }

                    if (empty($_POST["start"])) {
                        $errors['startEmpty'] = "Please set class start time!";
                    }
                    if (empty($_POST["end"])) {
                        $errors['endEmpty'] = "Please set class ending time!";
                    }
                    if(!empty($_POST["start"])){
                        if ($startLen > 8){
                            $errors['startFormat'] = "Your time format is not correct! Please insert like 12:00:PM";
                        }
                    }
                    if(!empty($_POST["end"])){
                        if ($endLen > 8){
                            $errors['endFormat'] = "Your time format is not correct! Please insert like 12:00:AM";
                        }
                    }
                    if (!empty($_POST["courseId"])){
                        $dptId = $_POST["deptId"];
                        $courseId = $_POST["courseId"];
                        $id = $_GET["editSchedule"];

                        $sql = "SELECT * FROM allocate_rooms WHERE course_id = '$courseId' AND day_id = '$dayId' AND id <> '$id' AND value=1";
                        $stmt = Database::Prepare($sql);
                        $stmt->execute();
                        $courseFound =  $stmt->fetchAll();
                        foreach ($courseFound as $course){
                            if ($course["course_id"] = $courseId) {

                                $errors['courseMatch'] = "This course already  scheduled today! Please select another course.";
                            }
                        }
                    }

                    if (!empty($_POST["start"]) && !empty($_POST["end"])){
                        $postStart = strtotime($_POST["start"]);
                        $postEnd = strtotime($_POST["end"]);
                        $id = $_GET["editSchedule"];

                        if ($postStart == strtotime("12:00 AM") || $postStart < strtotime("05:00 AM") ||
                            $postEnd == strtotime("12:00 AM") || $postEnd < strtotime("05:00 AM") || $postStart > $postEnd){
                            $errors['wrongTime'] = "Maybe in your schedule time included 12:00AM - 05:59AM. It's not right time for class schedule.Please Check again.";
                        }

                        $sql = "SELECT allocate_rooms.*, rooms.room_no, days.title, courses.course_code FROM allocate_rooms LEFT JOIN rooms on 
allocate_rooms.room_id = rooms.id LEFT JOIN days ON allocate_rooms.day_id = days.id LEFT JOIN courses ON 
allocate_rooms.course_id=courses.id WHERE allocate_rooms.room_id = '$roomId' AND allocate_rooms.day_id = '$dayId' AND allocate_rooms.id <> '$id' AND value = 1  ";
                        $stmt = Database::Prepare($sql);
                        $stmt->execute();
                        $data =  $stmt->fetchAll();
                        $i = 0;
                        foreach ($data as $value){
                            $i++;
                            $databaseStart = strtotime($value["start"]);
                            $databaseEnd = strtotime($value["end"]);

                            if ($postStart >= $databaseStart && $postStart <= $databaseEnd ||
                                $postEnd >= $databaseStart && $postEnd <= $databaseEnd ||
                                $postStart <= $databaseStart && $postEnd >= $databaseEnd){


                                $newStart = $databaseStart;
                                $newEnd = $databaseEnd;
                                $startTime = date("g:i a", $newStart);
                                $endTime = date("g:i a", $newEnd);
                                $scheduleError[$i] = $value["room_no"] . " room allotment for " . $value["course_code"] . " from " .
                                    $startTime . " to " . $endTime . " on " . $value["title"];
                            }
                        }
                    }

                    if (empty($errors) && empty($scheduleError)){
                        $id = $_GET["editSchedule"];
                        $allocate->scheduleUpdate($_POST, $id );
                    }
                }
            }
            ?>
            <?Php
            if (isset($_POST["refresh"])){
                echo "<script>window.location = ''</script>";
            }
            ?>
            <div class="box-body">
                <?php
                if (!isset($_GET["editSchedule"])){
                    header("Location: view-schedule.php");
                }
                else{
                $id = $_GET["editSchedule"];
                $data = $allocate->getScheduleById($id);

                ?>
                <form action="" method="post" class="form-horizental col-sm-offset-4 col-sm-4 col-sm-offset-4">
                    <?php
                    echo Session::SuccessMsg();
                    echo Session::ErrorMsg();
                    ?>
                    <div class="form-group">
                        <label for="">Department</label>
                        <select name="deptId" id="deptId" class="form-control selectpicker" data-show-subtext="true"
                                data-live-search="true">
                            <option value="">&larr; Select Department &rarr;</option>
                            <?php
                            foreach (Department::getAllDepartment() as $val) { ?>
                                <option
                                    <?php
                                    if ($data['dept_id'] == $val['id']){
                                        echo "selected='selected'";
                                    } ?>
                                    value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
                            <?php } ?>
                        </select>
                        <?php if (!empty($errors['deptEmpty'])){
                            echo Utility::error($errors["deptEmpty"]);
                        } ?>
                    </div>


                    <div class="form-group">
                        <label for="">Course Name</label>
                        <select name="courseId" id="courseId" class="form-control selectpicker" data-show-subtext="true" data-live-search="true">
                            <option value="">&larr; Select Course Name &rarr;</option>
                            <?php foreach ($course->getCoursesBy_deptId($data['dept_id']) as $course){ ?>
                            <option
                                <?php
                                if ($data['course_id'] == $course['id']){
                                    echo "selected='selected'";
                                } ?>
                                value="<?php echo $course["id"] ?>"> <?php echo $course["course_name"] ?> </option>
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
                        <label for="">Room No</label>
                        <select name="roomId" class="form-control selectpicker" data-show-subtext="true"
                                data-live-search="true">
                            <option value="">&larr; Select Room &rarr;</option>
                            <?php
                            foreach (AllocateClass::getAll_rooms() as $value) { ?>
                                <option
                                    <?php
                                    if ($data['room_id'] == $value['id']){
                                        echo "selected='selected'";
                                    } ?>
                                    value="<?php echo $value['id']; ?>"><?php echo $value['room_no']; ?></option>
                            <?php } ?>
                        </select>
                        <?php if (!empty($errors['roomEmpty'])){
                            echo Utility::error($errors["roomEmpty"]);
                        } ?>
                        <?php if (!empty($scheduleError)){
                            echo Utility::errorMsz($scheduleError);
                        } ?>
                        <?php if (!empty($errors['wrongTime'])){
                            echo Utility::error($errors["wrongTime"]);
                        } ?>
                    </div>
                    <div class="form-group">
                        <label for="">Day</label>
                        <select name="dayId" class="form-control selectpicker" data-show-subtext="true"
                                data-live-search="true">
                            <option value="">&larr; Select Day &rarr;</option>
                            <?php
                            foreach (AllocateClass::getAll_days() as $value) { ?>
                                <option
                                    <?php
                                    if ($data['day_id'] == $value['id']){
                                        echo "selected='selected'";
                                    } ?>
                                    value="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></option>
                            <?php } ?>
                        </select>
                        <?php if (!empty($errors['dayEmpty'])){
                            echo Utility::error($errors["dayEmpty"]);
                        } ?>
                    </div>
                    <div id="totalCredit" class="form-group">
                        <label>From</label><br>
                        <input type="text" id="t1"  data-format="hh:mm A" name="start" value="<?php echo date("g:i a",
                            strtotime($data["start"]));
                        ?>" class="form-control">
                        <?php if (!empty($errors['startEmpty'])){
                            echo Utility::error($errors["startEmpty"]);
                        } ?>
                        <?php if (!empty($errors['startFormat'])){
                            echo Utility::error($errors["startFormat"]);
                        } ?>
                    </div>
                    <div class="form-group">
                        <label>To</label><br>
                        <input  type="text" id="t1"  data-format="hh:mm A" name="end" value="<?php echo date("g:i a",
                            strtotime($data["end"])); ?>" class="form-control">
                        <?php if (!empty($errors['endEmpty'])){
                            echo Utility::error($errors["endEmpty"]);
                        } ?>
                        <?php if (!empty($errors['endFormat'])){
                            echo Utility::error($errors["endFormat"]);
                        } ?>
                    </div>
                    <button type="submit" name="submit" class="btn
                    btn-info">Update</button>
                    <a href="view-schedule.php" name="refresh" class="btn btn-info">View
                        Schedule</a>
                </form>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<script>
    $(function(){
        $('#t1').clockface();
    });
</script>
<?php include "footer.php"?>

