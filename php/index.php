<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $answer1 = $answer2 = $answer3 = $answer4 = $answer5 = "";
$name_err = $answer1_err = $answer2_err = $answer3_err = $answer4_err = $answer5_err =  "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

     // Validate name
     $input_name = trim($_POST["name"]);
     if(empty($input_name)){
         $name_err = "Please enter a name.";
     } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
         $name_err = "Please enter a valid name.";
     } else{
         $name = $input_name;
     }
  
    // Validate answer1
     $input_answer1 = trim(isset($_POST["answer1"]));
     if(empty($input_answer1)){
         $answer1_err = "Please enter an answer.";     
     } else{
         $answer1 = $input_answer1;
     }

    // Validate answer2
    $input_answer2 = trim(isset($_POST["answer2"]));
    if(empty($input_answer2)){
        $answer2_err = "Please enter an answer.";     
    } else{
        $answer2 = $input_answer2;
    }

    // Validate answer3
    $input_answer3 = trim(isset($_POST["answer3"]));
    if(empty($input_answer3)){
        $answer3_err = "Please enter an answer.";     
    } else{
        $answer3 = $input_answer3;
    }

    // Validate answer4
    $input_answer4 = trim(isset($_POST["answer4"]));
    if(empty($input_answer4)){
        $answer4_err = "Please enter an answer.";     
    } else{
        $answer4 = $input_answer4;
    }

    // Validate answer5
    $input_answer5 = trim(isset($_POST["answer5"]));
    if(empty($input_answer5)){
        $answer5_err = "Please enter an answer.";     
    } else{
        $answer5 = $input_answer5;
    }
    

    // Check input errors before inserting in database
    if( empty($name_err) && empty($answer1_err) && empty($answer2_err) && empty($answer3_err) && empty($answer4_err) && empty($answer5_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO users_answer (name, answer1, answer2, answer3, answer4, answer5) VALUES (?,  ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssss", $_POST['name'], $_POST['answer1'], $_POST['answer2'], $_POST['answer3'], $_POST['answer4'], $_POST['answer5']);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();

}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="js/addbox.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="page-header">
    <img class="img-thumbnail" src="images/s.png" alt="sparck" style="height: 90px" >
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to SPARCK.</h1>
       
    </div>      
     <div >
           
    </div>
    <div class="wrapper  ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                   
                    <p>Please answers this questions and submit to add answers record to the database.</p>
                    <form action="index.php" method="post" class="text-left">

                    <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </di>
  <div class="row">
    <div class="col-md-6">
    <div class="form-group <?php echo (!empty($answer1_err)) ? 'has-error' : ''; ?>">
                            <label>1. Situation</label>
                            <textarea name="answer1" class="form-control" rows="5"><?php echo $answer1; ?></textarea>
                            <span class="help-block"><?php echo $answer1_err;?></span> 
                        </div>
    </div>
    <div class="col-md-6">
    <div class="form-group <?php echo (!empty($answer2_err)) ? 'has-error' : ''; ?>">
                            <label>2. Aim</label>
                            <textarea name="answer2" class="form-control" rows="5"><?php echo $answer2_err; ?></textarea>
                            <span class="help-block"><?php echo $answer2_err;?></span> 
                        </div>
    </div>
    <div class="col-md-6">
    <div class="form-group <?php echo (!empty($answer4_err)) ? 'has-error' : ''; ?>">
                            <label>4. Time period</label>
                            <textarea name="answer4" class="form-control" rows="5"><?php echo $answer4_err; ?></textarea>
                            <span class="help-block"><?php echo $answer4_err;?></span> 
                        </div>
    </div>
    <div class="col-md-6">
   
  <div class="form-group <?php echo (!empty($answer3_err)) ? 'has-error' : ''; ?>">
                            <label>3. Area of Focus</label>
                            <textarea name="answer3" class="form-control" rows="5"><?php echo $answer3_err; ?></textarea>
                            <span class="help-block"><?php echo $answer3_err;?></span> 
                        </div>

    </div>
    <div class="col-md-12">
    <div class="form-group <?php echo (!empty($answer5_err)) ? 'has-error' : ''; ?>">
                            <label>5. Name one local fish</label>
                            <textarea name="answer5" class="form-control" ><?php echo $answer5_err; ?></textarea>
                            <span class="help-block"><?php echo $answer5_err;?></span> 
                        </div>
    </div>
  </div>

                    <!------------------------------------->

                    <div>
                        <h4><b>Strength / What's working well?</b></h4>
                        <button type="button" id="append" name="append"  class="btn btn-primary btn-sm"  onclick="add_field()" value="AddTextbox">Add TextBox</button>
                        </div><br>
                        <div>
                    </div>
                    <form class="form-horizontal" method= "Post">
                    <div class="control-group">
                    <div class="inc">
                    <div class="controls">
                    <input type="text" class="form-control" name="textbox" placeholder="strenght"/> 
                    <input type="text" class="form-control" name="text" placeholder="strenght"/>
                
                    <br>
                    <br>
                </div>
                </div>
      
             </div>
            </form>

                    <!------------------------------------->
                      
        
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
                </div>
            </div>        
        </div>
    </div>

<script>
jQuery(document).ready( function () {
        $("#append").click( function(e) {
          e.preventDefault();
        $(".inc").append('<div class="controls">\
                <input class="form-control" type="text" name="textbox" placeholder="textbox">\
                <input class="form-control" type="text" name="text" placeholder="text">\
                <a href="#" class="remove_this btn btn-danger">remove</a>\
                <br>\
                <br>\
            </div>');
        return false;
        });

    jQuery(document).on('click', '.remove_this', function() {
        jQuery(this).parent().remove();
        return false;
        });
    $("input[type=submit]").click(function(e) {
      e.preventDefault();
      $(this).next("[name=textbox]")
      .val(
        $.map($(".inc :text"), function(el) {
          return el.value
        }).join(",\n")
      )
    })
  });
</script>
    
</body>
</html>