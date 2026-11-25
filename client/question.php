
<?php
require_once __DIR__ . '/../server/Commen/db.php';

// Fetch all questions, including the owner's user ID
$sql = "
    SELECT
        q.id,
        q.title,
        q.description,
        q.user_id,
        c.name AS category_name,
        u.username
    FROM questions q
    LEFT JOIN category c ON q.category_id = c.id
    LEFT JOIN datauser u ON q.user_id = u.id
    ORDER BY q.id DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Question query failed: " . $conn->error);
}
?>

<style>
.question-home-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.question-home-container h1 {
    text-align: center;
    color: #1e293b;
    margin-bottom: 10px;
}

.question-home-subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 30px;
}

.home-question-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transition: 0.2s ease;
}

.home-question-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.home-question-card h2 {
    font-size: 21px;
    color: #334155;
    margin-bottom: 12px;
}

.home-question-card p {
    color: #64748b;
    line-height: 1.7;
}

.home-question-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
    color: #4f46e5;
    font-size: 13px;
}

.view-question-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 18px;
    background: #4f46e5;
    color: #ffffff;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.2s ease;
}

.view-question-btn:hover {
    background: #3730a3;
    color: #ffffff;
}

.delete-question-form {
    display: inline-block;
    margin-left: 8px;
}

.delete-question-button {
    margin-top: 10px;
    padding: 10px 18px;
    background: white;
    color: #dc2626;
    border: 1px solid #dc2626;
    border-radius: 8px;
    cursor: pointer;
}

.delete-question-button:hover {
    background: #fee2e2;
}
</style>

<div class="question-home-container">

    <h1>Community Questions</h1>

    <p class="question-home-subtitle">
        Explore questions shared by our community.
    </p>

    <!-- Question Deleted Success Message -->
    <?php if (isset($_GET['question_deleted'])) { ?>
        <p style="color:green; text-align:center;">
            Main question and all its answers and replies deleted successfully!
        </p>
    <?php } ?>

    <?php if ($result->num_rows > 0) { ?>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div class="home-question-card">

                <!-- Main Question Title -->
                <h2>
                    <?php echo htmlspecialchars($row['title']); ?>
                </h2>

                <!-- Question Description -->
                <p>
                    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                </p>

                <!-- View Question -->
                <a
                    href="/project1/?question-details=<?php echo (int)$row['id']; ?>"
                    class="view-question-btn">
                    View Question & Answers
                </a>

                <!-- Delete Main Question: Only Owner Can See -->
                <?php
                if (
                    isset($_SESSION['datauser']['id']) &&
                    (int)$_SESSION['datauser']['id'] === (int)$row['user_id']
                ) {
                ?>

                    <form
                        action="/project1/server/request.php"
                        method="POST"
                        class="delete-question-form"
                        onsubmit="return confirm('Are you sure you want to delete this MAIN QUESTION? All answers and nested replies will also be permanently deleted.');">

                        <input
                            type="hidden"
                            name="delete_question"
                            value="1">

                        <input
                            type="hidden"
                            name="question_id"
                            value="<?php echo (int)$row['id']; ?>">

                        <button
                            type="submit"
                            class="delete-question-button">
                            Delete Question
                        </button>

                    </form>

                <?php } ?>

                <!-- Question Information -->
                <div class="home-question-meta">

                    <span>
                        Category:
                        <?php echo htmlspecialchars(
                            $row['category_name'] ?? 'Uncategorized'
                        ); ?>
                    </span>

                    <span>
                        Asked by:
                        <?php echo htmlspecialchars(
                            $row['username'] ?? 'Unknown user'
                        ); ?>
                    </span>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p class="text-center">
            No questions available yet. Be the first to ask!
        </p>

    <?php } ?>

</div>

<?php
$result->free();
?>
