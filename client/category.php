
<?php
require_once __DIR__ . '/../server/Commen/db.php';

// Get all categories
$categoryQuery = "SELECT id, name FROM category ORDER BY name ASC";
$categoryResult = $conn->query($categoryQuery);

if (!$categoryResult) {
    die("Category query failed: " . $conn->error);
}

// Get selected category ID
$selectedCategory = isset($_GET['category_id'])
    ? (int) $_GET['category_id']
    : 0;

// Fetch questions for selected category
$questions = null;
$stmt = null;

if ($selectedCategory > 0) {

    $stmt = $conn->prepare("
        SELECT
            q.id,
            q.title,
            q.description,
            c.name AS category_name,
            u.username
        FROM questions q
        LEFT JOIN category c ON q.category_id = c.id
        LEFT JOIN datauser u ON q.user_id = u.id
        WHERE q.category_id = ?
        ORDER BY q.id DESC
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $selectedCategory);
    $stmt->execute();

    $questions = $stmt->get_result();
}
?>

<style>
.category-page {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.category-page h1 {
    text-align: center;
    color: #1e293b;
}

.category-filter {
    margin: 25px 0;
    text-align: center;
}

.category-filter select {
    width: 100%;
    max-width: 400px;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 16px;
}

.category-question-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px;
    margin-bottom: 18px;
}

.category-question-card h2 {
    color: #334155;
    font-size: 21px;
}

.category-question-card p {
    color: #64748b;
    line-height: 1.7;
}

.category-question-meta {
    margin-top: 15px;
    color: #4f46e5;
    font-size: 13px;
}

.category-view-btn {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 18px;
    background: #4f46e5;
    color: white;
    text-decoration: none;
    border-radius: 8px;
}

.category-view-btn:hover {
    background: #3730a3;
    color: white;
}
</style>

<div class="category-page">

    <h1>Browse Questions by Category</h1>

    <!-- Category filter form -->
    <form method="GET"
          action="/project1/"
          class="category-filter">

        <input type="hidden" name="Category" value="true">

        <select name="category_id"
                onchange="this.form.submit()"
                required>

            <option value="">Select a category</option>

            <?php while ($cat = $categoryResult->fetch_assoc()) { ?>

                <option
                    value="<?php echo (int)$cat['id']; ?>"
                    <?php echo ($selectedCategory === (int)$cat['id']) ? 'selected' : ''; ?>>

                    <?php echo htmlspecialchars($cat['name']); ?>

                </option>

            <?php } ?>

        </select>

    </form>

    <!-- Display questions -->
    <?php if ($selectedCategory === 0) { ?>

        <p style="text-align:center;">
            Please select a category to see its questions.
        </p>

    <?php } elseif ($questions && $questions->num_rows > 0) { ?>

        <?php while ($row = $questions->fetch_assoc()) { ?>

            <div class="category-question-card">

                <h2>
                    <?php echo htmlspecialchars($row['title']); ?>
                </h2>

                <p>
                    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                </p>

                <a class="category-view-btn"
                   href="/project1/?question-details=<?php echo (int)$row['id']; ?>">

                    View Question & Answers

                </a>

                <div class="category-question-meta">

                    Category:
                    <?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>

                    |

                    Asked by:
                    <?php echo htmlspecialchars($row['username'] ?? 'Unknown user'); ?>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p style="text-align:center;">
            No questions found in this category.
        </p>

    <?php } ?>

</div>

<?php
if ($stmt !== null) {
    $stmt->close();
}

$categoryResult->free();
?>
