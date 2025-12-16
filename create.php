<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $major = trim($_POST['major']);
    $academic_year = trim($_POST['academic_year']);
    $profile_image = null;

    $currentYearTH = date("Y") + 543;

    // --- Validation ---
    if (empty($fullname) || empty($email) || empty($major) || empty($academic_year)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } elseif ($academic_year > $currentYearTH) {
        $error = "ปีการศึกษา ($academic_year) ต้องไม่เกินปีปัจจุบัน ($currentYearTH)";
    } else {
        $stmtCheck = $conn->prepare("SELECT id FROM members WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->rowCount() > 0) {
            $error = "อีเมลนี้ ($email) มีผู้ใช้งานแล้ว กรุณาใช้อีเมลอื่น";
        }
    }

    // --- Upload & Save ---
    if (empty($error)) {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileSize = $_FILES['profile_image']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize < 5 * 1024 * 1024) {
                    $newFileName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $fileExtension;
                    $uploadFileDir = 'uploads/';
                    if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $profile_image = $newFileName;
                    }
                } else {
                    $error = "ไฟล์รูปใหญ่เกิน 5MB";
                }
            } else {
                $error = "นามสกุลไฟล์ไม่ถูกต้อง";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO members (fullname, email, major, academic_year, profile_image) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $major, $academic_year, $profile_image])) {
                header("Location: index.php");
                exit();
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
            }
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        /* --- Styles เดียวกับหน้า Dashboard เพื่อความต่อเนื่อง --- */
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #495057;
            padding: 20px 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header h2 {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* ตกแต่ง Input fields */
        .form-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            background-color: #f8f9fa;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff;
            border-color: #6dd5ed;
            box-shadow: 0 0 0 4px rgba(33, 147, 176, 0.1);
        }

        /* ปุ่มกด */
        .btn-submit {
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(33, 147, 176, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 147, 176, 0.4);
            color: white;
        }

        .btn-back {
            background: #fff;
            border: 2px solid #e9ecef;
            color: #6c757d;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #e9ecef;
            color: #495057;
        }

        /* Upload Area Styling */
        .upload-area {
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #fbfbfc;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .upload-area:hover {
            border-color: #6dd5ed;
            background: #f0faff;
        }

        .upload-icon {
            font-size: 2rem;
            color: #adb5bd;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <div class="glass-card">
                    <div class="form-header">
                        <div class="mb-2">
                            <i class="bi bi-person-plus-fill fs-1 opacity-50"></i>
                        </div>
                        <h2>NEW MEMBER</h2>
                        <p class="small opacity-75 mb-0">เพิ่มสมาชิกใหม่เข้าสู่ระบบ DevClub</p>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center mb-4 rounded-3">
                                <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                                <div><strong>ขออภัย!</strong> <?= $error ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">

                            <div class="mb-4">
                                <label class="form-label">รูปโปรไฟล์</label>
                                <div class="upload-area" onclick="document.getElementById('profile_image').click();">
                                    <i class="bi bi-cloud-arrow-up-fill upload-icon d-block"></i>
                                    <span class="text-muted small">คลิกเพื่ออัปโหลดรูปภาพ (Max 5MB)</span>
                                    <input type="file" name="profile_image" id="profile_image" class="form-control d-none" accept="image/*" onchange="previewFile()">
                                    <div id="file-name" class="mt-2 text-primary small fw-bold"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label"><i class="bi bi-person me-2"></i>ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" name="fullname" class="form-control" placeholder="ระบุชื่อและนามสกุล" required value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label"><i class="bi bi-envelope me-2"></i>อีเมล <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label class="form-label"><i class="bi bi-mortarboard me-2"></i>สาขาวิชา <span class="text-danger">*</span></label>
                                    <select name="major" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกสาขาวิชา --</option>
                                        <?php
                                        $majors = [
                                            "วิทยาการคอมพิวเตอร์ (CS)",
                                            "เทคโนโลยีสารสนเทศ (IT)",
                                            "วิศวกรรมซอฟต์แวร์ (SE)",
                                            "วิศวกรรมคอมพิวเตอร์ (CPE)",
                                            "วิทยาการข้อมูล (DS)",
                                            "เทคโนโลยีธุรกิจดิจิทัล",
                                            "เทคโนโลยีมัลติมีเดีย"
                                        ];
                                        foreach ($majors as $m) {
                                            $selected = (isset($_POST['major']) && $_POST['major'] == $m) ? 'selected' : '';
                                            echo "<option value='$m' $selected>$m</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label"><i class="bi bi-calendar-event me-2"></i>ปีการศึกษา <span class="text-danger">*</span></label>
                                    <input type="number" name="academic_year" class="form-control" placeholder="<?= date("Y") + 543 ?>" required value="<?= isset($_POST['academic_year']) ? htmlspecialchars($_POST['academic_year']) : '' ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-submit">
                                    <i class="bi bi-check-lg me-2"></i>บันทึกข้อมูล
                                </button>
                                <a href="index.php" class="btn btn-back text-center text-decoration-none">
                                    ย้อนกลับ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function previewFile() {
            const input = document.getElementById('profile_image');
            const fileNameDisplay = document.getElementById('file-name');
            if (input.files.length > 0) {
                fileNameDisplay.textContent = 'Selected: ' + input.files[0].name;
                fileNameDisplay.classList.add('animate-pulse');
            } else {
                fileNameDisplay.textContent = '';
            }
        }
    </script>
</body>

</html>