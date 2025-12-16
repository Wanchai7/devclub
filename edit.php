<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $major = $_POST['major'];
    $academic_year = $_POST['academic_year'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateStmt = $conn->prepare("UPDATE members SET fullname=?, email=?, major=?, academic_year=? WHERE id=?");
        if ($updateStmt->execute([$fullname, $email, $major, $academic_year, $id])) {
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
    <title>แก้ไขข้อมูล - DevClub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">แก้ไขข้อมูลสมาชิก</div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label>ชื่อ-นามสกุล</label>
                                <input type="text" name="fullname" class="form-control" value="<?= $member['fullname'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>อีเมล</label>
                                <input type="email" name="email" class="form-control" value="<?= $member['email'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>สาขาวิชา</label>
                                <input type="text" name="major" class="form-control" value="<?= $member['major'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>ปีการศึกษา (พ.ศ.)</label>
                                <input type="number" name="academic_year" class="form-control" value="<?= $member['academic_year'] ?>" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">อัปเดตข้อมูล</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-2">ยกเลิก</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>