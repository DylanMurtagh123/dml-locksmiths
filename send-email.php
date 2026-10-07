<?php

// -----------------------------------------------
// DML Locksmiths - Contact Form Handler
// -----------------------------------------------

$recipient_email = "dmllocksmiths.uk@gmail.com";

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

// Collect and sanitise inputs
$name     = isset($_POST['name'])     ? htmlspecialchars(strip_tags(trim($_POST['name'])))     : '';
$phone    = isset($_POST['phone'])    ? htmlspecialchars(strip_tags(trim($_POST['phone'])))    : '';
$email    = isset($_POST['email'])    ? htmlspecialchars(strip_tags(trim($_POST['email'])))    : '';
$service  = isset($_POST['service'])  ? htmlspecialchars(strip_tags(trim($_POST['service'])))  : 'Not selected';
$location = isset($_POST['location']) ? htmlspecialchars(strip_tags(trim($_POST['location']))) : 'Not provided';
$message  = isset($_POST['message'])  ? htmlspecialchars(strip_tags(trim($_POST['message'])))  : 'No message provided';

// Basic validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required.";
}

if (empty($phone)) {
    $errors[] = "Phone number is required.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// Work out where to send the user back to
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.html';

// If validation fails, go back with errors
if (!empty($errors)) {
    $error_string = urlencode(implode(" | ", $errors));
    header("Location: " . $redirect_url . "?status=error&message=" . $error_string);
    exit;
}

// Build the email
$subject = "New Enquiry from $name - DML Locksmiths Website";

$email_body  = "You have received a new enquiry from the DML Locksmiths website.\n\n";
$email_body .= "----------------------------------------\n";
$email_body .= "CUSTOMER DETAILS\n";
$email_body .= "----------------------------------------\n";
$email_body .= "Name:     $name\n";
$email_body .= "Phone:    $phone\n";
$email_body .= "Email:    " . (!empty($email) ? $email : "Not provided") . "\n\n";

$email_body .= "----------------------------------------\n";
$email_body .= "ENQUIRY DETAILS\n";
$email_body .= "----------------------------------------\n";
$email_body .= "Service:  " . (!empty($service) ? $service : "Not selected") . "\n";
$email_body .= "Location: " . (!empty($location) ? $location : "Not provided") . "\n\n";
$email_body .= "Message:\n" . (!empty($message) ? $message : "No message provided") . "\n\n";

$email_body .= "----------------------------------------\n";
$email_body .= "This email was sent from the contact form on the DML Locksmiths website.\n";

// Headers
$headers  = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: " . (!empty($email) ? $email : $recipient_email) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send the email
$mail_sent = mail($recipient_email, $subject, $email_body, $headers);

// Redirect with success or error
if ($mail_sent) {
    header("Location: thank-you.html");
} else {
    header("Location: " . $redirect_url . "?status=error&message=" . urlencode("Sorry, there was a problem sending your message. Please call Sean directly on 01473 875026."));
}

exit;

?>