<?php 
// Start session 
session_start(); 
 
// Include and initialize DB class 
require_once 'Json.class.php'; 
$db = new Json(); 
 
// Set default redirect url 
$redirectURL = 'index.php'; 
 
if(isset($_POST['userSubmit'])){ 
    // Get form fields value 
    $id = $_POST['id']; 
	$firstname = trim(strip_tags($_POST['firstname'])); 
    $lastname = trim(strip_tags($_POST['lastname'])); 
    $email = trim(strip_tags($_POST['email'])); 
    $phone = trim(strip_tags($_POST['phone'])); 
    $zipcode = trim(strip_tags($_POST['zipcode'])); 
     
    $id_str = ''; 
    if(!empty($id)){ 
        $id_str = '?id='.$id; 
    } 
     
    // Fields validation 
    $errorMsg = ''; 
    if(empty($firstname)){ 
        $errorMsg .= '<p>Please enter your first name.</p>'; 
    } 
    if(empty($lastname)){ 
        $errorMsg .= '<p>Please enter your last name.</p>'; 
    } 
	if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){ 
        $errorMsg .= '<p>Please enter a valid email.</p>'; 
    } 
    if(empty($phone)){ 
        $errorMsg .= '<p>Please enter contact no.</p>'; 
    } 
    if(empty($zipcode)){ 
        $errorMsg .= '<p>Please enter a zipcode.</p>'; 
    } 
     
    // Submitted form data 
    $userData = array( 
        'firstname' => $firstname, 
		'lastname' => $lastname, 
        'email' => $email, 
        'phone' => $phone, 
        'zipcode' => $zipcode 
    ); 
     
    // Store the submitted field value in the session 
    $sessData['userData'] = $userData; 
     
    // Submit the form data 
    if(empty($errorMsg)){ 
        if(!empty($_POST['id'])){ 
            // Update user data 
            $update = $db->update($userData, $_POST['id']); 
             
            if($update){ 
                $sessData['status']['type'] = 'success'; 
                $sessData['status']['msg'] = 'Data has been updated successfully.'; 
                 
                // Remove submitted fields value from session 
                unset($sessData['userData']); 
            }else{ 
                $sessData['status']['type'] = 'error'; 
                $sessData['status']['msg'] = 'Some problem occurred, please try again.'; 
                 
                // Set redirect url 
                $redirectURL = 'addEdit.php'.$id_str; 
            } 
        }else{ 
            // Insert user data 
            $insert = $db->insert($userData); 
             
            if($insert){ 
                $sessData['status']['type'] = 'success'; 
                $sessData['status']['msg'] = 'Data has been added successfully.'; 
                 
                // Remove submitted fields value from session 
                unset($sessData['userData']); 
            }else{ 
                $sessData['status']['type'] = 'error'; 
                $sessData['status']['msg'] = 'Some problem occurred, please try again.'; 
                 
                // Set redirect url 
                $redirectURL = 'addEdit.php'.$id_str; 
            } 
        } 
    }else{ 
        $sessData['status']['type'] = 'error'; 
        $sessData['status']['msg'] = '<p>Please fill all the mandatory fields.</p>'.$errorMsg; 
         
        // Set redirect url 
        $redirectURL = 'addEdit.php'.$id_str; 
    } 
     
    // Store status into the session 
    $_SESSION['sessData'] = $sessData; 
}elseif(($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])){ 
    // Delete data 
    $delete = $db->delete($_GET['id']); 
     
    if($delete){ 
        $sessData['status']['type'] = 'success'; 
        $sessData['status']['msg'] = 'Data has been deleted successfully.'; 
    }else{ 
        $sessData['status']['type'] = 'error'; 
        $sessData['status']['msg'] = 'Some problem occurred, please try again.'; 
    } 

    // Store status into the session 
    $_SESSION['sessData'] = $sessData; 
} 

// Redirect to the respective page 
header("Location:".$redirectURL); 
exit(); 
?>