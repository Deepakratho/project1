
<?php
require_once __DIR__ . '/../server/Commen/db.php';

// Get question ID
$questionId = filter_input(
    INPUT_GET,
    'question-details',
    FILTER_VALIDATE_INT
);

if (!$questionId || $questionId < 1) {
    die("Invalid question ID.");
}

// Fetch question
$sql = "
    SELECT
        q.id,
        q.title,
        q.description,
        c.name AS category_name,
        u.username
    FROM questions q
    LEFT JOIN category c ON q.category_id = c.id
    LEFT JOIN datauser u ON q.user_id = u.id
    WHERE q.id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Question query failed: " . $conn->error);
}

$stmt->bind_param("i", $questionId);
$stmt->execute();

$question = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$question) {
    die("Question not found.");
}

// Fetch every answer and reply
$answerSql = "
    SELECT
        a.id,
        a.parent_id,
        a.user_id,
        a.answer,
        a.created_at,
        u.username
    FROM answers a
    LEFT JOIN datauser u ON a.user_id = u.id
    WHERE a.question_id = ?
    ORDER BY a.id ASC
";

$answerStmt = $conn->prepare($answerSql);

if (!$answerStmt) {
    die("Answer query failed: " . $conn->error);
}

$answerStmt->bind_param("i", $questionId);
$answerStmt->execute();

$result = $answerStmt->get_result();

$childrenMap = [];
$totalAnswers = 0;

// Group answers by parent ID
while ($row = $result->fetch_assoc()) {

    $parentId = (int)($row['parent_id'] ?? 0);

    $childrenMap[$parentId][] = $row;

    $totalAnswers++;
}


// Recursive function to display answers and nested replies
function displayAnswers($parentId, $childrenMap, $questionId, $level = 0) {

    if (!isset($childrenMap[$parentId])) {
        return;
    }

    foreach ($childrenMap[$parentId] as $row) {

        $answerId = (int)$row['id'];
        ?>

        <div class="answer-card"
             style="margin-left: <?php echo min($level * 25, 150); ?>px;">

            <h3>
                <?php echo htmlspecialchars(
                    $row['username'] ?? 'Unknown user'
                ); ?>
            </h3>

            <p>
                <?php echo nl2br(
                    htmlspecialchars($row['answer'])
                ); ?>
            </p>

            <div class="answer-date">
                Answered on:
                <?php echo htmlspecialchars($row['created_at'] ?? ''); ?>
            </div>

            <?php if (isset($_SESSION['datauser']['id'])) { ?>

                <!-- Reply Button -->
                <button
                    type="button"
                    class="reply-toggle"
                    onclick="toggleReplyForm(<?php echo $answerId; ?>)">
                    Reply
                </button>

                <!-- Delete Button: Only for answer owner -->
                <?php
                if (
                    (int)$_SESSION['datauser']['id'] ===
                    (int)$row['user_id']
                ) {
                ?>

                    <form action="/project1/server/request.php"
                          method="POST"
                          class="delete-answer-form"
                          onsubmit="return confirm('Are you sure? This will delete this answer and all replies underneath it.');">

                        <input type="hidden"
                               name="delete_answer"
                               value="1">

                        <input type="hidden"
                               name="answer_id"
                               value="<?php echo $answerId; ?>">

                        <input type="hidden"
                               name="question_id"
                               value="<?php echo (int)$questionId; ?>">

                        <button type="submit"
                                class="delete-answer-button">
                            Delete
                        </button>

                    </form>

                <?php } ?>

                <!-- Reply Form -->
                <div
                    id="reply-form-<?php echo $answerId; ?>"
                    class="reply-form"
                    style="display:none;">

                    <form action="/project1/server/request.php"
                          method="POST">

                        <input type="hidden"
                               name="submit_answer"
                               value="1">

                        <input type="hidden"
                               name="question_id"
                               value="<?php echo (int)$questionId; ?>">

                        <input type="hidden"
                               name="parent_id"
                               value="<?php echo $answerId; ?>">

                        <textarea
                            name="answer"
                            placeholder="Write your reply..."
                            maxlength="5000"
                            required></textarea>

                        <button type="submit">
                            Submit Reply
                        </button>

                    </form>

                </div>

            <?php } ?>

            <?php
            // Display all replies to this answer
            displayAnswers(
                $answerId,
                $childrenMap,
                $questionId,
                $level + 1
            );
            ?>

        </div>

        <?php
    }
}
?>

<style>
.details-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.details-question-card,
.answer-card,
.answer-form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}

.details-question-card h1 {
    color: #1e293b;
    font-size: 27px;
    margin-bottom: 15px;
}

.details-question-card p,
.answer-card p {
    color: #64748b;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.details-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
    color: #4f46e5;
    font-size: 13px;
}

.answer-card h3 {
    color: #334155;
    font-size: 17px;
}

.answer-form-card textarea,
.reply-form textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    resize: vertical;
    margin: 12px 0;
    box-sizing: border-box;
}

.answer-form-card button,
.reply-form button {
    padding: 11px 22px;
    border: none;
    border-radius: 8px;
    background: #4f46e5;
    color: white;
    cursor: pointer;
}

.answer-form-card button:hover,
.reply-form button:hover {
    background: #3730a3;
}

.reply-toggle {
    padding: 6px 14px;
    margin-top: 8px;
    border: 1px solid #4f46e5;
    border-radius: 7px;
    color: #4f46e5;
    background: white;
    cursor: pointer;
}

.reply-toggle:hover {
    background: #eef2ff;
}

.delete-answer-form {
    display: inline;
}

.delete-answer-button {
    padding: 6px 14px;
    margin-top: 8px;
    margin-left: 5px;
    border: 1px solid #dc2626;
    border-radius: 7px;
    color: #dc2626;
    background: white;
    cursor: pointer;
}

.delete-answer-button:hover {
    background: #fee2e2;
}

.reply-form {
    margin-top: 10px;
}

.answer-date {
    font-size: 12px;
    color: #94a3b8;
}

.answer-success {
    background: #dcfce7;
    color: #166534;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>

<div class="details-container">

    <!-- Selected Question -->
    <div class="details-question-card">

        <h1>
            <?php echo htmlspecialchars($question['title']); ?>
        </h1>

        <p>
            <?php echo nl2br(
                htmlspecialchars($question['description'])
            ); ?>
        </p>

        <div class="details-meta">

            <span>
                Category:
                <?php echo htmlspecialchars(
                    $question['category_name'] ?? 'Uncategorized'
                ); ?>
            </span>

            <span>
                Asked by:
                <?php echo htmlspecialchars(
                    $question['username'] ?? 'Unknown user'
                ); ?>
            </span>

        </div>

    </div>

    <!-- Original Answer Form -->
    <div class="answer-form-card">

        <h2>Write Your Answer</h2>

        <?php if (isset($_GET['answered'])) { ?>
            <div class="answer-success">
                Your answer or reply was submitted successfully!
            </div>
        <?php } ?>

        <?php if (isset($_GET['deleted'])) { ?>
            <div class="answer-success">
                Answer and all its nested replies deleted successfully!
            </div>
        <?php } ?>

        <?php if (isset($_SESSION['datauser']['id'])) { ?>

            <form action="/project1/server/request.php"
                  method="POST">

                <input type="hidden"
                       name="submit_answer"
                       value="1">

                <input type="hidden"
                       name="question_id"
                       value="<?php echo (int)$questionId; ?>">

                <input type="hidden"
                       name="parent_id"
                       value="">

                <textarea
                    name="answer"
                    placeholder="Write your answer here..."
                    maxlength="5000"
                    required></textarea>

                <button type="submit">
                    Submit Answer
                </button>

            </form>

        <?php } else { ?>

            <p>
                Please
                <a href="/project1/?login=true">login</a>
                to write an answer or reply.
            </p>

        <?php } ?>

    </div>

    <!-- All Answers and Nested Replies -->
    <h2>
        Community Answers (<?php echo $totalAnswers; ?>)
    </h2>

    <?php if ($totalAnswers > 0) { ?>

        <?php displayAnswers(0, $childrenMap, $questionId); ?>

    <?php } else { ?>

        <p>No answers yet. Be the first person to answer!</p>

    <?php } ?>

    <a href="/project1/?question=true"
       class="btn btn-secondary">
        Back to Questions
    </a>

</div>

<script>
function toggleReplyForm(answerId) {

    const form = document.getElementById(
        'reply-form-' + answerId
    );

    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php
$answerStmt->close();
?>
