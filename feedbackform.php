<?php
require_once 'database.php'; require_once("auth.php");
$visitId = $_GET['visit_id'] ?? null;
$visit = $visitId ? get_visit($visitId) : null;

authenticate("customer");

if (!$visit) {
    echo "<p>Visit not found or not specified.</p>";
    include 'footer.php';     exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $visit) {
    $comment = $_POST['comment'] ?? '';
    $rating = $_POST['rating'] ?? '';

   

    $feedbackData = [
        'comment' => $comment,
        'rating' => $rating,
        'visit_id' => $visitId
    ];

    if (insert_feedback($feedbackData)) {
        header('location: index.php');
    } else {
        echo "<p>Error submitting feedback.</p>";
    }
}

include 'header.php'; 
?>

<div class="container">
    <h2>Feedback for Visit ID: <?php echo $visitId; ?></h2>
    <form action="feedbackform.php?visit_id=<?php echo $visitId; ?>" method="post">
        <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <textarea class="form-control" id="comment" name="comment" required></textarea>
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <select class="form-select" id="rating" name="rating" required>
                <option value="">Select a rating</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

<?php
include 'footer.php'; ?>
