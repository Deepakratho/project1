
<?php
require_once __DIR__ . '/../server/Commen/db.php';

$query = "SELECT id, name FROM category ORDER BY name ASC";
$result = $conn->query($query);

if (!$result) {
    die("Category query failed: " . $conn->error);
}
?>

<select class="category-select"
        name="category"
        id="category"
        required>

    <option value="" selected disabled>
        Select a category
    </option>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <option value="<?php echo (int)$row['id']; ?>">
            <?php echo htmlspecialchars($row['name']); ?>
        </option>
    <?php } ?>

</select>

<?php $result->free(); ?>
