
<?php
// Check whether the user is logged in
if (!isset($_SESSION['datauser']['id'])) {
    echo "<p>Please log in to view your questions.</p>";
    return;
}

require_once __DIR__ . '/../server/Commen/db.php';

$userId = (int) $_SESSION['datauser']['id'];

// Get only questions posted by the logged-in user
$questionQuery = $conn->prepare("
    SELECT
        q.id,
        q.title,
        q.description,
        c.name AS category_name
    FROM questions q
    LEFT JOIN category c ON q.category_id = c.id
    WHERE q.user_id = ?
    ORDER BY q.id DESC
");

if (!$questionQuery) {
    die("Question query failed: " . $conn->error);
}

$questionQuery->bind_param("i", $userId);
$questionQuery->execute();

$questions = $questionQuery->get_result();


// Recursive function to display answers and unlimited replies
function displayMyAnswers($parentId, $childrenMap, $questionId, $currentUserId, $level = 0) {

    if (!isset($childrenMap[$parentId])) {
        return;
    }

    foreach ($childrenMap[$parentId] as $answer) {

        $answerId = (int) $answer['id'];
        ?>

        <div class="my-answer-card"
             style="margin-left: <?php echo min($level * 25, 150); ?>px;">

            <div class="my-answer-author">
                <?php
                echo htmlspecialchars(
                    $answer['username'] ?? 'Unknown user'
                );
                ?>
            </div>

            <div class="my-answer-text">
                <?php echo nl2br(htmlspecialchars($answer['answer'])); ?>
            </div>

            <div class="my-answer-date">
                <?php echo htmlspecialchars($answer['created_at'] ?? ''); ?>
            </div>

            <?php if ((int) $answer['user_id'] === $currentUserId) { ?>
                <form class="my-delete-answer-form"
                      action="/project1/server/request.php"
                      method="POST"
                      onsubmit="return confirm('Are you sure? This will delete this answer and all replies underneath it.');">

                    <input type="hidden" name="delete_answer" value="1">
                    <input type="hidden" name="answer_id" value="<?php echo $answerId; ?>">
                    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">
                    <input type="hidden" name="return_to" value="my-questions">

                    <button type="submit" class="my-delete-answer-button">
                        Delete
                    </button>
                </form>
            <?php } ?>

            <?php if (isset($_SESSION['datauser']['id'])) { ?>

                <button
                    type="button"
                    class="my-reply-toggle"
                    onclick="toggleMyReply(<?php echo $answerId; ?>)">
                    Reply
                </button>

                <div
                    id="my-reply-form-<?php echo $answerId; ?>"
                    class="my-nested-reply-form"
                    style="display:none;">

                    <form action="/project1/server/request.php"
                          method="POST">

                        <input type="hidden"
                               name="submit_answer"
                               value="1">

                        <input type="hidden"
                               name="question_id"
                               value="<?php echo $questionId; ?>">

                        <input type="hidden"
                               name="parent_id"
                               value="<?php echo $answerId; ?>">

                        <input type="hidden"
                               name="return_to"
                               value="my-questions">

                        <textarea
                            name="answer"
                            placeholder="Write your reply..."
                            maxlength="5000"
                            required></textarea>

                        <button type="submit" class="my-reply-button">
                            Submit Reply
                        </button>

                    </form>

                </div>

            <?php } ?>

            <?php
            // Display replies to this answer recursively
            displayMyAnswers(
                $answerId,
                $childrenMap,
                $questionId,
                $currentUserId,
                $level + 1
            );
            ?>

        </div>

        <?php
    }
}
?>

<style>
.my-questions-container {
    max-width: 950px;
    margin: 40px auto;
    padding: 20px;
}

.my-questions-heading {
    text-align: center;
    color: #4338ca;
    margin-bottom: 10px;
}

.my-questions-subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 30px;
}

.my-question-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 25px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.05);
}

.my-question-card h2 {
    color: #334155;
    font-size: 22px;
    margin-bottom: 12px;
}

.my-question-description {
    color: #64748b;
    line-height: 1.7;
}

.my-question-meta {
    color: #4f46e5;
    font-size: 13px;
    margin-top: 15px;
}

.my-replies-heading {
    margin-top: 25px;
    margin-bottom: 15px;
    color: #334155;
    font-size: 18px;
}

.my-answer-card {
    background: #f8fafc;
    border-left: 4px solid #6366f1;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow-wrap: anywhere;
}

.my-answer-author {
    color: #4338ca;
    font-weight: 700;
    margin-bottom: 8px;
}

.my-answer-text {
    color: #475569;
    line-height: 1.6;
    white-space: pre-wrap;
}

.my-answer-date {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 8px;
}

.my-reply-form {
    margin-top: 20px;
}

.my-reply-form label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.my-reply-form textarea,
.my-nested-reply-form textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    box-sizing: border-box;
    resize: vertical;
    font-family: Arial, sans-serif;
    font-size: 14px;
    margin-top: 10px;
}

.my-reply-button {
    margin-top: 12px;
    padding: 11px 20px;
    border: none;
    border-radius: 8px;
    background: #4f46e5;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.my-reply-button:hover {
    background: #3730a3;
}

.my-reply-toggle {
    margin-top: 10px;
    padding: 6px 14px;
    border: 1px solid #4f46e5;
    border-radius: 7px;
    color: #4f46e5;
    background: white;
    cursor: pointer;
}

.my-reply-toggle:hover {
    background: #eef2ff;
}

.my-delete-answer-form {
    display: inline-block;
    margin: 10px 0 0 8px;
}

.my-delete-answer-button {
    padding: 6px 14px;
    border: 1px solid #dc2626;
    border-radius: 7px;
    color: #dc2626;
    background: #fff;
    cursor: pointer;
}

.my-delete-answer-button:hover {
    color: #fff;
    background: #dc2626;
}

.my-delete-question-form {
    margin-top: 12px;
}

.my-delete-question-button {
    padding: 9px 16px;
    border: 1px solid #dc2626;
    border-radius: 8px;
    color: #dc2626;
    background: #fff;
    cursor: pointer;
    font-weight: 600;
}

.my-delete-question-button:hover {
    color: #fff;
    background: #dc2626;
}

.my-nested-reply-form {
    margin-top: 10px;
}

.my-no-questions {
    text-align: center;
    padding: 35px;
    color: #64748b;
    background: #f8fafc;
    border-radius: 12px;
}
</style>

<div class="my-questions-container">

    <h1 class="my-questions-heading">My Questions</h1>

    <p class="my-questions-subtitle">
        View your questions and conversations with the community.
    </p>

    <?php if (isset($_GET['answered'])) { ?>
        <p style="color:green; text-align:center;">
            Your answer or reply was submitted successfully!
        </p>
    <?php } ?>

    <?php if (isset($_GET['deleted'])) { ?>
        <p style="color:green; text-align:center;">
            Answer and all its nested replies deleted successfully!
        </p>
    <?php } ?>

    <?php if (isset($_GET['question_deleted'])) { ?>
        <p style="color:green; text-align:center;">
            Main question and all its answers and replies deleted successfully!
        </p>
    <?php } ?>

    <?php if ($questions->num_rows > 0) { ?>

        <?php while ($question = $questions->fetch_assoc()) { ?>

            <?php
            $questionId = (int) $question['id'];

            // Fetch all answers and replies for this question
            $answerQuery = $conn->prepare("
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
            ");

            if (!$answerQuery) {
                die("Answer query failed: " . $conn->error);
            }

            $answerQuery->bind_param("i", $questionId);
            $answerQuery->execute();

            $answerResult = $answerQuery->get_result();

            // Group answers by their parent ID
            $childrenMap = [];

            while ($answer = $answerResult->fetch_assoc()) {

                $parentId = (int) ($answer['parent_id'] ?? 0);

                $childrenMap[$parentId][] = $answer;
            }

            $answerQuery->close();

            // Count all answers and replies
            $totalAnswers = 0;

            foreach ($childrenMap as $group) {
                $totalAnswers += count($group);
            }
            ?>

            <div class="my-question-card">

                <h2>
                    <?php echo htmlspecialchars($question['title']); ?>
                </h2>

                <p class="my-question-description">
                    <?php
                    echo nl2br(htmlspecialchars($question['description']));
                    ?>
                </p>

                <!-- Delete Main Question (this page only shows the logged-in user's questions) -->
                <form class="my-delete-question-form"
                      action="/project1/server/request.php"
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to permanently delete this main question and all its answers and nested replies?');">

                    <input type="hidden" name="delete_question" value="1">
                    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">

                    <button type="submit" class="my-delete-question-button">
                        Delete Main Question
                    </button>
                </form>

                <div class="my-question-meta">
                    Category:
                    <?php
                    echo htmlspecialchars(
                        $question['category_name'] ?? 'Uncategorized'
                    );
                    ?>
                </div>

                <h3 class="my-replies-heading">
                    Community Answers (<?php echo $totalAnswers; ?>)
                </h3>

                <?php if ($totalAnswers > 0) { ?>

                    <?php
                    // Display original answers and all nested replies
                    displayMyAnswers(
                        0,
                        $childrenMap,
                        $questionId,
                        $userId
                    );
                    ?>

                <?php } else { ?>

                    <p style="color:#94a3b8;">
                        No answers yet. Be the first to answer!
                    </p>

                <?php } ?>

                <!-- Original Answer Form -->
                <form class="my-reply-form"
                      action="/project1/server/request.php"
                      method="POST">

                    <input type="hidden"
                           name="submit_answer"
                           value="1">

                    <input type="hidden"
                           name="question_id"
                           value="<?php echo $questionId; ?>">

                    <input type="hidden"
                           name="parent_id"
                           value="">

                    <input type="hidden"
                           name="return_to"
                           value="my-questions">

                    <label for="reply-<?php echo $questionId; ?>">
                        Write Your Answer
                    </label>

                    <textarea
                        name="answer"
                        id="reply-<?php echo $questionId; ?>"
                        placeholder="Write your answer here..."
                        maxlength="5000"
                        required></textarea>

                    <button type="submit" class="my-reply-button">
                        Submit Answer
                    </button>

                </form>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="my-no-questions">
            <h3>You haven't asked any questions yet.</h3>
            <p>Ask your first question and start a conversation!</p>

            <a href="/project1/?ask=true">
                Ask a Question
            </a>
        </div>

    <?php } ?>

</div>

<script>
function toggleMyReply(answerId) {

    const form = document.getElementById(
        'my-reply-form-' + answerId
    );

    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php
$questionQuery->close();
?>
