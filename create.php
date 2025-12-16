<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $major = $_POST['major'];
    $academic_year = $_POST['academic_year'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO members (fullname, email, major, academic_year) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$fullname, $email, $major, $academic_year])) {
            header("Location: index.php");
            exit();
        }
    } else {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสมาชิก - DevClub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">เพิ่มสมาชิกใหม่</div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label>ชื่อ-นามสกุล</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>สาขาวิชา</label>
                                <input type="text" name="major" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>ปีการศึกษา (พ.ศ.)</label>
                                <input type="number" name="academic_year" class="form-control" placeholder="เช่น 2567" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">บันทึกข้อมูล</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-2">ย้อนกลับ</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>