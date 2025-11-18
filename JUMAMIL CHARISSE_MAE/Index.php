<?php
session_start();
require_once __DIR__ . '/db.php';

$students = [];
$dbError = '';
try {
    $pdo = getDb();
    $stmt = $pdo->query('SELECT id, name, email FROM students ORDER BY id');
    $students = $stmt->fetchAll();
} catch (Exception $e) {
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container">
    <a href="logout.php" class="logout-btn">Log Out</a>
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin! Here you can manage the list of students.</p>
    

    <section class="Student">
        <?php if ($dbError): ?>
            <p style="color:darkred">Database error: <?php echo htmlspecialchars($dbError, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody id="studentsBody">
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $s): ?>
                        <tr data-id="<?php echo htmlspecialchars($s['id'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); ?>">
                            <td><?php echo htmlspecialchars($s['id'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s['name'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s['email'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        
        <div id="addStudentModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <button id="addStudentClose" class="modal-close" aria-label="Close">&times;</button>
                <h2>Add Student</h2>
                <form id="addStudentForm">
                    <div class="form-row">
                        <label for="add-name">Name</label>
                        <input id="add-name" name="name" type="text" required />
                    </div>
                    <div class="form-row">
                        <label for="add-email">Email</label>
                        <input id="add-email" name="email" type="email" required />
                    </div>
                    <div class="form-row">
                        <button type="submit" class="action-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>

        
        
        <div id="editStudentModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <button id="editStudentClose" class="modal-close" aria-label="Close">&times;</button>
                <h2>Edit Student</h2>
                <form id="editStudentForm">
                    <input type="hidden" name="id" />
                    <div class="form-row">
                        <label for="edit-name">Name</label>
                        <input id="edit-name" name="name" type="text" required />
                    </div>
                    <div class="form-row">
                        <label for="edit-email">Email</label>
                        <input id="edit-email" name="email" type="email" required />
                    </div>
                    <div class="form-row">
                        <button type="submit" class="action-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-actions">
            <button id="addstudent" class="action-btn">Add Student</button>
            <button id="editStudent" class="action-btn">Edit</button>
            <button id="deleteStudent" class="action-btn">Delete</button>
        </div>
        <script src="students.js"></script>

    </section>
</body>
</html>
