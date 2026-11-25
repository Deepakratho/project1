
<?php
session_start();

require_once __DIR__ . '/Commen/db.php';


// ==================== SIGN UP ====================
if (isset($_POST['signup'])) {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        die("Please fill in all required fields.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $datauser = $conn->prepare(
        "INSERT INTO datauser (username, email, password, address)
         VALUES (?, ?, ?, ?)"
    );

    if (!$datauser) {
        die("Signup prepare failed: " . $conn->error);
    }

    $datauser->bind_param(
        "ssss",
        $username,
        $email,
        $hashedPassword,
        $address
    );

    if ($datauser->execute()) {

        $newUserId = $conn->insert_id;
        $datauser->close();

        session_regenerate_id(true);

        $_SESSION['datauser']['id'] = $newUserId;
        $_SESSION['datauser']['username'] = $username;

        header("Location: /project1/");
        exit();

    } else {
        die("Signup error: " . $datauser->error);
    }
}


// ==================== LOGIN ====================
if (isset($_POST['login-identifier'], $_POST['password'])) {

    $identifier = trim($_POST['login-identifier']);
    $password = $_POST['password'];

    $login = $conn->prepare(
        "SELECT id, username, email, password
         FROM datauser
         WHERE email = ? OR username = ?
         LIMIT 1"
    );

    if (!$login) {
        die("Login prepare failed: " . $conn->error);
    }

    $login->bind_param("ss", $identifier, $identifier);
    $login->execute();

    $result = $login->get_result();
    $user = $result->fetch_assoc();

    $login->close();

    if (!$user) {
        header("Location: /project1/?login=true&error=not_registered");
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        header("Location: /project1/?login=true&error=wrong_password");
        exit();
    }

    session_regenerate_id(true);

    $_SESSION['datauser']['id'] = $user['id'];
    $_SESSION['datauser']['username'] = $user['username'];

    header("Location: /project1/");
    exit();
}


// ==================== ASK QUESTION ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ask_question'])) {

    if (!isset($_SESSION['datauser']['id'])) {
        header("Location: /project1/?login=true");
        exit();
    }

    $title = trim($_POST['question_title'] ?? '');
    $description = trim($_POST['question_description'] ?? '');

    $categoryId = filter_var(
        $_POST['category'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (
        $title === '' ||
        $description === '' ||
        $categoryId === false ||
        $categoryId === null ||
        $categoryId < 1
    ) {
        die("Please enter a title, description, and valid category.");
    }

    if (strlen($title) > 250) {
        die("Question title cannot exceed 250 characters.");
    }

    if (strlen($description) > 300) {
        die("Question description cannot exceed 300 characters.");
    }

    $userId = (int) $_SESSION['datauser']['id'];

    $question = $conn->prepare(
        "INSERT INTO questions (title, description, category_id, user_id)
         VALUES (?, ?, ?, ?)"
    );

    if (!$question) {
        die("Question prepare failed: " . $conn->error);
    }

    $question->bind_param(
        "ssii",
        $title,
        $description,
        $categoryId,
        $userId
    );

    if ($question->execute()) {

        $question->close();

        header("Location: /project1/?latestq=1&success=1");
        exit();

    } else {
        die("Question submission failed: " . $question->error);
    }
}


// ==================== SUBMIT ANSWER / NESTED REPLY ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answer'])) {

    // Check login
    if (!isset($_SESSION['datauser']['id'])) {
        header("Location: /project1/?login=true");
        exit();
    }

    $questionId = filter_var(
        $_POST['question_id'] ?? '',
        FILTER_VALIDATE_INT
    );

    $answer = trim($_POST['answer'] ?? '');

    // Parent ID: NULL means original answer.
    // A positive ID means reply to another answer/reply.
    $parentIdInput = trim((string)($_POST['parent_id'] ?? ''));

    $parentId = null;

    if ($parentIdInput !== '') {

        $validatedParentId = filter_var(
            $parentIdInput,
            FILTER_VALIDATE_INT
        );

        if (
            $validatedParentId === false ||
            $validatedParentId < 1
        ) {
            die("Invalid parent answer ID.");
        }

        $parentId = (int)$validatedParentId;
    }

    // Validate answer
    if (
        $questionId === false ||
        $questionId === null ||
        $questionId < 1 ||
        $answer === ''
    ) {
        die("Please enter a valid question ID and answer.");
    }

    if (strlen($answer) > 5000) {
        die("Your answer cannot exceed 5000 characters.");
    }

    // Check whether question exists
    $checkQuestion = $conn->prepare(
        "SELECT id FROM questions WHERE id = ?"
    );

    if (!$checkQuestion) {
        die("Question check failed: " . $conn->error);
    }

    $checkQuestion->bind_param("i", $questionId);
    $checkQuestion->execute();

    $questionExists = $checkQuestion->get_result()->num_rows > 0;

    $checkQuestion->close();

    if (!$questionExists) {
        die("The question you are answering does not exist.");
    }

    // Check that parent belongs to this question
    if ($parentId !== null) {

        $checkParent = $conn->prepare(
            "SELECT id
             FROM answers
             WHERE id = ? AND question_id = ?"
        );

        if (!$checkParent) {
            die("Parent answer check failed: " . $conn->error);
        }

        $checkParent->bind_param(
            "ii",
            $parentId,
            $questionId
        );

        $checkParent->execute();

        $parentExists = $checkParent->get_result()->num_rows > 0;

        $checkParent->close();

        if (!$parentExists) {
            die("The answer you are replying to does not belong to this question.");
        }
    }

    $userId = (int) $_SESSION['datauser']['id'];

    // Insert original answer or nested reply
    $answerQuery = $conn->prepare(
        "INSERT INTO answers
            (question_id, user_id, parent_id, answer)
         VALUES (?, ?, ?, ?)"
    );

    if (!$answerQuery) {
        die("Answer prepare failed: " . $conn->error);
    }

    $answerQuery->bind_param(
        "iiis",
        $questionId,
        $userId,
        $parentId,
        $answer
    );

    if ($answerQuery->execute()) {

        $answerQuery->close();

        if (($_POST['return_to'] ?? '') === 'my-questions') {

            header("Location: /project1/?my-questions=true&answered=1");

        } else {

            header(
                "Location: /project1/?question-details=" .
                $questionId . "&answered=1"
            );
        }

        exit();

    } else {

        die("Answer submission failed: " . $answerQuery->error);
    }
}


// ==================== DELETE ANSWER / REPLY THREAD ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_answer'])) {

    // Check login
    if (!isset($_SESSION['datauser']['id'])) {
        header("Location: /project1/?login=true");
        exit();
    }

    $userId = (int) $_SESSION['datauser']['id'];

    $answerId = filter_var(
        $_POST['answer_id'] ?? '',
        FILTER_VALIDATE_INT
    );

    $questionId = filter_var(
        $_POST['question_id'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (
        $answerId === false || $answerId === null || $answerId < 1 ||
        $questionId === false || $questionId === null || $questionId < 1
    ) {
        die("Invalid answer or question ID.");
    }

    // Verify ownership
    $checkOwner = $conn->prepare(
        "SELECT id
         FROM answers
         WHERE id = ? AND question_id = ? AND user_id = ?"
    );

    if (!$checkOwner) {
        die("Ownership check failed: " . $conn->error);
    }

    $checkOwner->bind_param(
        "iii",
        $answerId,
        $questionId,
        $userId
    );

    $checkOwner->execute();

    $isOwner = $checkOwner->get_result()->num_rows > 0;

    $checkOwner->close();

    if (!$isOwner) {
        die("You are not allowed to delete this answer.");
    }

    // Fetch all answers belonging to this question
    $getAnswers = $conn->prepare(
        "SELECT id, parent_id
         FROM answers
         WHERE question_id = ?"
    );

    if (!$getAnswers) {
        die("Answer lookup failed: " . $conn->error);
    }

    $getAnswers->bind_param("i", $questionId);
    $getAnswers->execute();

    $result = $getAnswers->get_result();

    // Build parent-child relationships
    $children = [];

    while ($row = $result->fetch_assoc()) {

        $parentId = (int)($row['parent_id'] ?? 0);

        $children[$parentId][] = (int)$row['id'];
    }

    $getAnswers->close();

    // Collect selected answer and all nested replies
    $deleteIds = [];
    $queue = [$answerId];
    $visited = [];

    while (!empty($queue)) {

        $currentId = array_pop($queue);

        if (isset($visited[$currentId])) {
            continue;
        }

        $visited[$currentId] = true;

        $deleteIds[] = $currentId;

        if (isset($children[$currentId])) {

            foreach ($children[$currentId] as $childId) {
                $queue[] = $childId;
            }
        }
    }

    // Delete children before their parent
    $deleteIds = array_reverse($deleteIds);

    // Start transaction
    $conn->begin_transaction();

    try {

        $deleteQuery = $conn->prepare(
            "DELETE FROM answers
             WHERE id = ? AND question_id = ?"
        );

        if (!$deleteQuery) {
            throw new Exception($conn->error);
        }

        foreach ($deleteIds as $id) {

            $deleteQuery->bind_param("ii", $id, $questionId);

            if (!$deleteQuery->execute()) {
                throw new Exception($deleteQuery->error);
            }
        }

        $deleteQuery->close();

        $conn->commit();

    } catch (Exception $e) {

        $conn->rollback();

        die("Delete failed: " . htmlspecialchars($e->getMessage()));
    }

    // Redirect after deletion
    if (($_POST['return_to'] ?? '') === 'my-questions') {

        header("Location: /project1/?my-questions=true&deleted=1");

    } else {

        header(
            "Location: /project1/?question-details=" .
            $questionId . "&deleted=1"
        );
    }

    exit();
}



// ==================== DELETE MAIN QUESTION ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {

    // Only logged-in users can delete questions
    if (!isset($_SESSION['datauser']['id'])) {
        header("Location: /project1/?login=true");
        exit();
    }

    $userId = (int) $_SESSION['datauser']['id'];

    $questionId = filter_var(
        $_POST['question_id'] ?? '',
        FILTER_VALIDATE_INT
    );

    if ($questionId === false || $questionId === null || $questionId < 1) {
        die("Invalid question ID.");
    }

    // Verify that the logged-in user owns this question
    $checkOwner = $conn->prepare(
        "SELECT id
         FROM questions
         WHERE id = ? AND user_id = ?"
    );

    if (!$checkOwner) {
        die("Question ownership check failed: " . $conn->error);
    }

    $checkOwner->bind_param("ii", $questionId, $userId);
    $checkOwner->execute();

    $isOwner = $checkOwner->get_result()->num_rows > 0;
    $checkOwner->close();

    if (!$isOwner) {
        http_response_code(403);
        die("You are not allowed to delete this question.");
    }

    // Delete all answers/replies first, then delete the question
    $conn->begin_transaction();

    try {
        $deleteAnswers = $conn->prepare(
            "DELETE FROM answers WHERE question_id = ?"
        );

        if (!$deleteAnswers) {
            throw new Exception($conn->error);
        }

        $deleteAnswers->bind_param("i", $questionId);

        if (!$deleteAnswers->execute()) {
            throw new Exception($deleteAnswers->error);
        }

        $deleteAnswers->close();

        $deleteQuestion = $conn->prepare(
            "DELETE FROM questions WHERE id = ? AND user_id = ?"
        );

        if (!$deleteQuestion) {
            throw new Exception($conn->error);
        }

        $deleteQuestion->bind_param("ii", $questionId, $userId);

        if (!$deleteQuestion->execute()) {
            throw new Exception($deleteQuestion->error);
        }

        if ($deleteQuestion->affected_rows !== 1) {
            throw new Exception("The question could not be deleted.");
        }

        $deleteQuestion->close();

        $conn->commit();

        header("Location: /project1/?question=true&question_deleted=1");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Question deletion failed: " . htmlspecialchars($e->getMessage()));
    }
}

// ==================== UNKNOWN REQUEST ====================
http_response_code(400);
echo "No matching form submission was detected.";

?>
