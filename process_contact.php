<?php
include 'includes/db.php';

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response['message'] = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address';
    } else {
        // Insert into database
        $sql = "INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $subject, $message, $ip_address);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Your message has been sent successfully. We will get back to you soon!';
        } else {
            $response['message'] = 'Error sending message. Please try again later.';
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
