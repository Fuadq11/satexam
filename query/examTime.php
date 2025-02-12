<?php
 session_start(); 
 include("../conn.php");
 extract($_POST);
 date_default_timezone_set("Asia/Baku");


$exmne_id = $_SESSION['examineeSession']['exmne_id'];
$res = array();
$res["examstatus"] = 0;
$ex_part = "math_time";
if($part == 1){
    $ex_part = "en_time";
}
$sql = "SELECT ".$ex_part."  FROM sessions WHERE exam_id = '$exam_id' AND examin_id = '$exmne_id'";
$result = $conn->query($sql);
$data = $result->fetch(PDO::FETCH_ASSOC);

$current_time = new DateTime();
$end_time = new DateTime($data[$ex_part]);
$remaining_time = $end_time->getTimestamp() - $current_time->getTimestamp();
// $remaining_time = $data['math_time'];

if ($remaining_time <= 0) {
    $remaining_time = 0;
    if($part == 0){
        $queryEndMathPart = $conn->query("UPDATE exam_attempt SET math_part_status = 1 WHERE exam_id = '$exam_id' AND exmne_id = '$exmne_id'");
        $res["examstatus"] = 1;
    }else if($part == 1){
        $queryEndMathPart = $conn->query("UPDATE exam_attempt SET en_part_status = 1 WHERE exam_id = '$exam_id' AND exmne_id = '$exmne_id'");
        $res["examstatus"] = 2;
    }
    
}
$res["remaining_time"] = $remaining_time;
echo json_encode($res);





 ?>


 