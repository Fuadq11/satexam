<?php 
  date_default_timezone_set("Asia/Baku");
  require "../conn.php";
    session_start();
    if(!isset($_SESSION['examineeSession']['exmne_id']) || empty($_SESSION['examineeSession']['exmne_id']) || !isset($_GET['id']) || empty($_GET['id'])){
      echo "<h1>Error 404. Page not found</h1>";

    }else{
      $exmne_id = $_SESSION['examineeSession']['exmne_id'];
    // if(isset($_SESSION['math_time']) && isset($_SESSION['en_time'])){

    // }
    
?> 
<script type="text/javascript" >
   function preventBack(){window.history.forward();}
    setTimeout("preventBack()", 0);
    window.onunload=function(){null};
</script>
 <?php 
     if(!isset($_GET['id']) || empty($_GET['id'])){
        echo "<h1>Error 404. Page not found</h1>";
     }else{
        $examId = $_GET['id'];
     };
    $selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId' ")->fetch(PDO::FETCH_ASSOC);

    
     

    
    
    // $exDisplayLimit = 10;
    $question_number = 1;
    $part = 0;
    
   
     $queryExamAtmp = $conn->query("SELECT * FROM exam_attempt WHERE exam_id='$examId' AND exmne_id = '$exmne_id'");
     $checkExamAtmp = $queryExamAtmp->fetch(PDO::FETCH_ASSOC);
     if($queryExamAtmp->rowCount()>0){
        if($checkExamAtmp['math_part_status']!=0){
          $part = 1;
        }
        if ($checkExamAtmp['en_part_status']!=0){
          $part = 2;
        }
     }  
    
     if($part == 2){
      echo "<h1>Already taken!</h1>"; ?>
      <script>window.location.href="../index.php";</script>
    <?php }else{
      $math_time = $selExam['ex_math_time_limit'];
      $en_time = $selExam['ex_en_time_limit'];
      $selExamTimeLimit = null;
      // $dateNow = strtotime(times().' + 5 minute');
      
      // $current_time = date('Y-m-d H:i:s',$dateNow);
      
      //  Select Exam time limit
        $queryExamTime = $conn->query("SELECT * FROM sessions WHERE exam_id='$examId' AND examin_id = '$exmne_id' ");
      if($queryExamTime->rowCount()>0){
        $selExamTime =  $queryExamTime->fetch(PDO::FETCH_ASSOC);
        if($part == 0){
          $selExamTimeLimit = $selExamTime['math_time'];
            }else if($part == 1){
          if($selExamTime['en_time']==null){
            $current_time = new DateTime('NOW');
            $current_time->add(DateInterval::createFromDateString($en_time.' minute'));
            $current_time = (string) $current_time->format("Y-m-d H:i:s");
            $insertExamTime = $conn->query("UPDATE sessions SET en_time = ('$current_time') WHERE exam_id = '$examId'AND examin_id = '$exmne_id'");
          }else{
            $selExamTimeLimit = $selExamTime['en_time'];
          }
        }  
       }else{
            $current_time = new DateTime('NOW');
            $current_time->add(DateInterval::createFromDateString($math_time.' minute'));
            $current_time = (string) $current_time->format("Y-m-d H:i:s");
            $insertExamTime = $conn->query("INSERT INTO  sessions(exam_id,examin_id,math_time) VALUES ('$examId','$exmne_id','$current_time')");
            $examAttempt = $conn->query("INSERT INTO exam_attempt(exmne_id,exam_id,math_part_status,en_part_status) VALUES ('$exmne_id','$examId',0,0)");
            // $selExamTime =  $queryExamTime->fetch(PDO::FETCH_ASSOC);
      
            // $selExamTimeLimit = $selExamTime['math_time'];
        }  
      
 ?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>1To1EDU</title>
    <meta name="description" content="Medical Schools in Italy are becoming trend in recent years. In 2011 Italian Universities started to offer MBBS in English for international students.">
    <link rel="stylesheet" href="css/style.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="../js/jquery.js"></script>
  </head>
  <body>
    <main>
   
      <header id="" class="fixed-top test-page-header" width="100%">
        <div class="row ">
          <div class="custom-header-l d-inline-block col-sm-4 col-md-4 pt-3 pb-3">
            <!-- <img src="img/logo.png" width="auto" height="40" class="d-inline-block align-top ml-2" alt=""> -->
            <h5 class="d-inline-block ml-3 mt-2">Sat Exam</h5>
          </div>
          <div class="col-sm-4 col-md-4 pt-3 pb-3">
              <div class="page-title text-center">
                  <h4 style="color:#fff"><?php echo $selExam['ex_title']; ?></h4>
                  <div class="page-title-subheading" style="color:#fff">
                    <?php echo $selExam['ex_description']; ?>
                  </div>
              </div>
          </div>
          <div class="d-inline-block col-sm-4 col-md-4 pt-3 pb-3">
            <div class="custom-header-r  float-md-right ml-2 mr-2  text-right">
              <span id="global_watch" class="globaltimer"><?=$selExamTimeLimit?></span>
              <a class="stopwatch"><i class="fas fa-stopwatch"></i></a>
              <a href="#" class="close-btn"><i class="fas fa-times"></i></a>
            </div>
          </div>
        </div>
      </header>
      <section id="question-section">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <form id="practice_form" name="practice_from" class="" method="post">
                <input type="hidden" name="exam_id" id="exam_id" value="<?php echo $examId; ?>">
                <input type="hidden" name="examAction" id="examAction" >
                <input type="hidden" name="examPart" id="examPart" value="<?=$part?>">
                
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="panel-body-inner">
                      <?php 
                          $selQuest = $conn->query("SELECT * FROM exam_question_tbl WHERE exam_id='$examId' AND exam_part='$part' ORDER BY rand()");
                          
                          if($selQuest->rowCount() > 0)
                          {
                              $total_question = $selQuest->rowCount();
                          while ($selQuestRow = $selQuest->fetch(PDO::FETCH_ASSOC)) { ?>
                      <?php $questId = $selQuestRow['eqt_id']; 
                            $selQuestImgs = $conn->query("SELECT * FROM question_images WHERE question_id='$questId' ");
                      ?>
                      <!-- Question begins -->
                        <div class="row question" data-questionnumber="<?=$question_number?>" data-questionid="<?=$question_number?>" data-questiontype="<?=$selQuestRow['question_type']?>">
                        
                          <div class="col-md-12">

                            <div class="question-number">

                              <span class="number"><?=$question_number++?></span> <span class="total">/<?=$total_question?></span>

                            </div>

                          </div>

                          <div class="col-md-6 question2wrapper">

                            <div class="question2">
                              <?=$selQuestRow['question_detail']; ?>  
                              <?php 
                                if($selQuestImgs->rowCount() > 0)
                              { 
                                while ($questImg = $selQuestImgs->fetch(PDO::FETCH_ASSOC)) { 
                                ?>
                              <p><img alt="" src="../assets/images/question-images/<?=$questImg['img_name']?>" class="img-responsive"></p>
                              <?php } }?>
                              </div>
                          </div>
                          <br class="visible-xs">
                          <div class="col-md-6 questioncontainer">
                            <div class="actualquestion"><p><?=$selQuestRow['exam_question']?></p><p></p>
                            </div>
                            <?php
                                if($selQuestRow['question_type']==0){
                            ?>
                            <div class="option-items" onclick="">
                                <div class="question_answer" onclick="" data-question-id="<?=$questId?>" data-answer-value="a">
                                <div class="abcde text-capitalize">a</div>
                                <div class="question-option"><p></p><p><?=$selQuestRow['exam_ch1']?></p>
                                </div>
                              </div>
                                <div class="question_answer" onclick="" data-question-id="<?=$questId?>" data-answer-value="b">
                                <div class="abcde text-capitalize">b</div>
                                <div class="question-option"><p></p><p><?=$selQuestRow['exam_ch2']?></p>
                              </div>
                              </div>
                                <div class="question_answer" onclick="" data-question-id="<?=$questId?>" data-answer-value="c">
                                <div class="abcde text-capitalize">c</div>
                                <div class="question-option"><p></p><p><?=$selQuestRow['exam_ch3']?></p>
                                </div>
                              </div>
                              <div class="question_answer" onclick="" data-question-id="<?=$questId?>" data-answer-value="d">
                                <div class="abcde text-capitalize">d</div>
                                <div class="question-option"><p></p><p><?=$selQuestRow['exam_ch4']?></p>
                                </div>
                              </div>
                              <ol type="A" style="display: none" >
                                  <li><input type="radio" name="question[<?=$questId?>]" value="a"></li>
                                  <li><input type="radio" name="question[<?=$questId?>]" value="b"></li>
                                  <li><input type="radio" name="question[<?=$questId?>]" value="c"></li>
                                  <li><input type="radio" name="question[<?=$questId?>]" value="d"></li>
                              </ol>
                            </div>

                            <?php }else{ ?>
                              <div class="open-question-asnwer my-4">
                                <label>Answer: </label>
                                  <input type="text" name="question[<?=$questId?>]" >
                              </div>

                         <?php   } ?>
                            <button class="btn btn-primary anwer-save-btn" onclick="saveAnswer(<?=$questId?>,<?=$examId?>,<?=$selQuestRow['question_type']?>)" data-question-id="<?=$question_number?>" type="button">Save Answer</button>
                          </div>
                        </div>
                        <?php } ?>
                      <!-- Question ends -->
                      </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>
    </section>
    <!--   Bottom bar -->
    <div class="container-fluid">

      <div class="navbar-fixed-bottom normal-row">

        <div class="panel-footer bottombar">

          <div class=" nopadding">

            <div class="previousButton">Previous</div>

          </div>

          <div class=" nopadding">

            <div class="nextButton">Next</div>
            <span style="display:none"><input class="submitButton" type="submit" value="Finish"></span>
            <div class="fakeSubmitButton" style="display: none;"><span class="glyphicon glyphicon-ok" style="margin-right: 10px;margin-left: -10px;"></span> Finish</div>

          </div>

          <div class="pagination-wrapper nopadding">

            <div class="backscroll" ><i class="fas fa-chevron-left"></i></div>

            <ul class="pagination">
               
            
                

                <li class="bottombar-questions bottombar-highlight" data-questionid="1" data-questionnumber="1" onclick="showOnlyQuestion(1)">1</li>
                  
                <?php if($total_question>0){
                  $j=2;
                  while($j<=$total_question){
                  ?>
                <li class="bottombar-questions" data-questionid="<?=$j?>" data-questionnumber="<?=$j?>" onclick="showOnlyQuestion(<?=$j?>)"><?=$j?></li>

                <?php $j++; } }?>
            </ul>

            <div class="forwardscroll hidden-xs" ><i class="fas fa-chevron-right"></i></div>
          </div>

        </div>

      </div>

    </div>
    <?php } ?>
  </div>
<!-- /container -->

    </main>
    <script> var totalQuestions = <?=$total_question?>; var exam_id = <?=$examId?>; var part = <?=$part?>;</script>
    <script src="js/custom.js"></script>
    <script src="https://kit.fontawesome.com/b32d5a037c.js" crossorigin="anonymous"></script>
    <script src="../js/jquery.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js" ></script>
    <script src="js/sweetalert.js"></script>
  </body>

</html>
<?php   
} } ?>