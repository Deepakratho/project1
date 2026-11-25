
<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();

    header("Location: /project1/");
    exit();
}

// Protect My Questions page
if (
    isset($_GET['my-questions']) &&
    !isset($_SESSION['datauser']['id'])
) {
    header("Location: /project1/?login=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Project 1</title>

    <?php include('client/commonFiles.php'); ?>
</head>

<body>

<?php

// Navigation bar
include('client/header.php');


// 1. Individual question details and answers
if (isset($_GET['question-details'])) {

    include('client/question-details.php');


// 2. My Questions - only for logged-in users
} elseif (isset($_GET['my-questions'])) {

    include('client/my-questions.php');


// 3. Home page - display all questions
} elseif (isset($_GET['question'])) {

    include('client/question.php');


// 4. Login page - only when logged out
} elseif (
    isset($_GET['login']) &&
    !isset($_SESSION['datauser']['username'])
) {

    include('client/login.php');


// 5. Signup page - only when logged out
} elseif (
    isset($_GET['signup']) &&
    !isset($_SESSION['datauser']['username'])
) {

    include('client/signup.php');


// 6. Latest questions
} elseif (isset($_GET['latestq'])) {

    include('client/latestq.php');


// 7. Category page
} elseif (isset($_GET['Category'])) {

    include('client/Category.php');


// 8. Ask Question - only when logged in
} elseif (
    isset($_GET['ask']) &&
    isset($_SESSION['datauser']['username'])
) {

    include('client/ask.php');


// 9. Default home page
} else {

    include('client/question.php');

}

?>

</body>
</html>
