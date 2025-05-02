<?php
require_once 'php/includes/auth.php';

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']); // safe output
$user_role = $_SESSION['user_role'];
requireLogin();
requireRole('admin');



// Example of how to use the addAuditLog function

require_once 'php/includes/db.php'; // Database connection
require_once 'php/includes/add_audit.php'; // Audit log functions
$student_id = 123;

addAuditLog($pdo, 'DELETE', 'students', $student_id, 'Deleted student record.');


// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;port=3307;dbname=finance", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// fetch data from the database
$stmt = $pdo->query("SELECT * FROM report ORDER BY id DESC LIMIT 1");
$report = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$report) {
    die("No report data found.");
}
$stmt = $pdo->query("SELECT * FROM pangalawa ORDER BY id DESC LIMIT 1");
$pangalawa = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pangalawa) {
    die("No table data found.");
}
$stmt = $pdo->query("SELECT * FROM totals ORDER BY id DESC LIMIT 1");
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$totals) {
    die("No table data found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/fms-style.css">
    <link rel="shortcut icon" href="./assets/favicon/SIS.ico" type="image/x-icon">
    <title>Finance Management</title>
</head>
<style>

</style>

<body>
    <header>
        <div class="header-content">
            <img src="./assets/img/SIS-logo.png" alt="Student Information System Logo">
            <div>
                <h1>Student Information System</h1>
                <h2>Finance</h2>
            </div>
        </div>
    </header>
    <main class="main">
        <aside class="sidebar">
            <nav aria-label="Main navigation">
                <ul>
                    <li><a href="student-fees.php"><span class="mdi mdi-account-school-outline"></span><span>Student
                                Fees</span></a>
                    </li>
                    <li><a href="billing-invoicing.php"><span class="mdi mdi-invoice-list-outline"></span><span>Billing
                                Invoicing</span></a>
                    </li>
                    <li><a href="scholarship.php"><span class="mdi mdi-certificate-outline"></span>
                            <span>Scholarship</span></a></li>
                    <li><a href="refund.php"><span class="mdi mdi-cash-refund"></span> <span>Refund</span></a></li>
                    <li><a href="financial-report.php"><span class="mdi mdi-finance"></span> <span>Financial
                                Report</span></a></li>
                    <li><a href="audit-trail.php"><span class="mdi mdi-monitor-eye"></span> <span>Audit
                                Trail</span></a></li>
                </ul>
            </nav>
            <nav aria-label="User options">
                <ul>
                    <li><a href="./php/logout.php"><span class="mdi mdi-logout"></span> <span>Logout</span></a></li>
                </ul>
            </nav>
            </nav>
        </aside>
        <section class="content" style="padding: 1rem;">
            <article class="module-content"></article>

            <div class="tabs">
                <div class="tab">Monthly</div>
                <div class="tab">Annual</div>
            </div>

            <div class="date-picker">
                <label for="from">From:</label>
                <input type="date" id="from" name="from">
                <label for="to">To:</label>
                <input type="date" id="to" name="to">
            </div>

            <div class="kpi-cards">
                <?php if ($totals): ?>
                <div class="card">Total Collected: ₱<?= number_format($totals['t.collected']) ?></div>
                <div class="card">Total Due: ₱<?= number_format($totals['t.due']) ?></div>
                <div class="card">Total Scholarships Awarded: ₱<?= number_format($totals['t.scholar']) ?></div>
                <div class="card">Total Refunds: ₱<?= number_format($totals['t.refund']) ?></div>
                <?php else: ?>
                <div class="card">No data available</div>
                <?php endif; ?>
            </div>

            <table>
                <thead>
                <tr>
                    <th>Month</th>
                    <th>Collected</th>
                    <th>Due</th>
                    <th>Scholarships</th>
                    <th>Refunds</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($pangalawa): ?>
                    <tr>
                    <td><?= date("F", strtotime($pangalawa['month'])) ?></td>
                    <td>₱ <?= number_format($pangalawa['collected']) ?></td>
                    <td>₱ <?= number_format($pangalawa['due']) ?></td>
                    <td>₱ <?= number_format($pangalawa['scholarship']) ?></td>
                    <td>₱ <?= number_format($pangalawa['refund']) ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                    <td colspan="5">No data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if ($report): ?>
            <div class="detail-view">
                <h3>Report Details</h3>
                <p><strong>Report Period:</strong>
                <?= date("F j, Y", strtotime($report['period_from'])) ?> - <?= date("F j, Y", strtotime($report['period_to'])) ?>
                </p>
                <p><strong>Report Type:</strong> <?= htmlspecialchars($report['report_type']) ?></p>
                <p><strong>Summary:</strong> <?= nl2br(htmlspecialchars($report['summary'])) ?></p>
                <p><strong>Generated By:</strong> <?= htmlspecialchars($report['generated_by']) ?></p>
            </div>
            <?php else: ?>
            <div class="detail-view">
                <p>No report data available.</p>
            </div>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <address>
            <p>For inquiries please contact 000-0000<br>
                Email: sisfinance3220@gmail.com</p>
        </address>
        <p>&copy; 2025 Student Information System<br>All Rights Reserved</p>
    </footer>
    <script src="./assets/js/fms-script.js"></script>
    <script>
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                alert(`Switched to ${tab.textContent} Report`);
            });
        });
    </script>
</body>

</html>
