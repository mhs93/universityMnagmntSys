<?php
include ('../vendor/mpdf/mpdf/mpdf.php');
include "../vendor/autoload.php";
use App\Database\Database;

if (isset($_GET["pdfId"])){
    $sql= "SELECT students.reg_no AS no, students.title AS name, students.email , departments.id, departments.title FROM students LEFT JOIN departments on students.dept_id = 
departments.id WHERE students.id = '".$_GET["pdfId"]."'";
    $stmt = Database::Prepare($sql);
    $stmt->execute();
    $data =  $stmt->fetch();
    $out .='<label>Reg No:' . ' '.$data["no"].'</label><br>';
    $out .='<label>Name:' . ' '.$data["name"].'</label><br>';
    $out .='<label>Email:' . ' ' .$data["email"].'</label><br>';
    $out .='<label>Department:' . ' '.$data["title"] .'</label>';

    $query = "SELECT courses.id, courses.course_name, courses.course_code FROM entrol_course LEFT JOIN courses 
ON entrol_course.course_id=courses.id  WHERE entrol_course.reg_no = '" .$_GET["pdfId"]."'";
    $stmt = Database::Prepare($query);
    $stmt->execute();
    $data =  $stmt->fetchall();
    foreach ($data as $results) {
        $output .='<tr>';
        $output .= '<td>' . $results['course_name'] . '</td>';
        $output .= '<td>' . $results['course_code'] . '</td>';
        $output .= '<td>';

        $sql = "SELECT  grades.grade, results.id FROM results INNER JOIN grades ON results.grade=grades.id WHERE results.course_id = '" .$results["id"]."'";
        $stmt = Database::Prepare($sql);
        $stmt->execute();
        $totalRows =  $stmt->rowCount();
        $gradeLetter =  $stmt->fetchAll();
        if ($totalRows < 1){
            $output .='Not Graded Yet';
        }else {
            foreach ($gradeLetter as $grade) {
                $output .=  $grade['grade'];
            }
        }
        $output .='</td>';
        $output .='</tr>';
    }
}

$html = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
</head>
<body>

<div class="content-wrapper">
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border text-center">
            <h1 class="box-title">Turtles University Of Bangladesh</h1>
            <br>
            <p>Farmgate, Tejgaon, Dhaka-1215</p>
                <h2 class="box-title">Student Result</h2>
            </div>
            <div class="box-body">
                <div class="col-sm-offset-3 col-sm-6 col-sm-offset-3">
                    <div class="form-group">
                        $out
                    </div>
                    <br>
                    <div class="form-group">
                        <table id="table_info" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Course Code</th>
                                <th>Grade</th>
                            </tr>
                            </thead>
                            <tbody>
                            $output;
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
EOD;

$mpdf = new mPDF();
$mpdf->WriteHTML($html);
$mpdf->Output();
exit();

?>