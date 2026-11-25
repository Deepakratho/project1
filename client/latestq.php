
<?php
// Do not call session_start() here.
// index.php has already started the session.

require_once __DIR__ . '/../server/Commen/db.php';

// Get latest questions with category and username
$sql = "
    SELECT
        q.id,
        q.title,
        q.description,
        q.category_id,
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
    die('Latest questions query failed: ' . $conn->error);
}
?>

<style>
.latest-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.latest-container h1 {
    color: #1e293b;
    font-size: 30px;
    margin-bottom: 8px;
}

.latest-container > p {
    color: #64748b;
    margin-bottom: 25px;
}

.question-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 18px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transition: 0.2s ease;
}

.question-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.question-card h2 {
    color: #334155;
    font-size: 21px;
    margin: 0 0 12px;
}

.question-card p {
    color: #64748b;
    line-height: 1.7;
}

.question-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
    color: #4f46e5;
    font-size: 13px;
}
</style>

<div class="latest-container">

    <h1 class="text-center">Latest Questions</h1>

    <p>Recently submitted questions from our community.</p>

    <?php if ($result->num_rows > 0) { ?>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div class="question-card">

                <h2>
                    <?php echo htmlspecialchars($row['title']); ?>
                </h2>

                <p>
                    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                </p>

                <div class="question-meta">

                    <span>
                        Category:
                        <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                    </span>

                    <span>
                        Asked by:
                        <?php echo htmlspecialchars($row['username'] ?? 'Unknown user'); ?>
                    </span>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p>No questions have been submitted yet.</p>

    <?php } ?>

</div>

<?php
$result->free();
?>
