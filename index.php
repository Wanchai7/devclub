<?php
require_once 'db.php';

// --- Logic ค้นหา ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM members";
$params = [];

if (!empty($search)) {
    $sql .= " WHERE id = ? OR fullname LIKE ?";
    $params[] = $search;
    $params[] = "%$search%";
}

$sql .= " ORDER BY id ASC"; // เรียงน้อยไปมาก

$stmt = $conn->prepare($sql);
$stmt->execute($params);

// --- ฟังก์ชันสุ่มสี Badge ตามสาขา (เพื่อให้แต่ละสาขามีสีต่างกัน) ---
function getMajorColor($major)
{
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
    // ใช้ crc32 แปลงข้อความสาขาเป็นตัวเลข เพื่อให้ได้ index สีเดิมเสมอ
    $index = crc32($major) % count($colors);
    return $colors[$index];
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevClub Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        /* --- Global Styles --- */
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
            /* พื้นหลังสีคลีนๆ */
            min-height: 100vh;
            color: #495057;
        }

        /* --- Header & Logo --- */
        .club-title {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* --- Card Design --- */
        .main-card {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            /* เงานุ่มลึก */
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        /* --- Table Styling --- */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
            transition: all 0.2s;
        }

        .table-hover tbody tr:hover {
            background-color: #f8faff;
            transform: scale(1.005);
            /* ขยายแถวนิดนึงตอนชี้ */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            z-index: 10;
            position: relative;
        }

        /* --- Components --- */
        .member-thumb {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            /* เปลี่ยนเป็นสี่เหลี่ยมมน */
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .search-pill {
            border-radius: 50px;
            padding-left: 20px;
            border: 1px solid #e9ecef;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .search-pill:focus {
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(33, 147, 176, 0.1);
            border-color: #6dd5ed;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .btn-edit:hover {
            background-color: #ffc107;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-delete:hover {
            background-color: #dc3545;
            color: #fff;
            transform: translateY(-2px);
        }

        /* --- Animation --- */
        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-row {
            animation: fadeInRow 0.5s ease-out forwards;
            opacity: 0;
        }

        /* สลับ delay ให้แต่ละแถวขึ้นมาไม่พร้อมกัน */
        tr:nth-child(1) {
            animation-delay: 0.1s;
        }

        tr:nth-child(2) {
            animation-delay: 0.15s;
        }

        tr:nth-child(3) {
            animation-delay: 0.2s;
        }

        tr:nth-child(4) {
            animation-delay: 0.25s;
        }

        tr:nth-child(5) {
            animation-delay: 0.3s;
        }

        /* ... */
    </style>
</head>

<body>
    <div class="container py-5">

        <div class="row align-items-center mb-5">
            <div class="col-md-6 d-flex align-items-center">
                <div class="bg-white p-2 rounded-4 shadow-sm me-3">
                    <svg width="50" height="50" viewBox="0 0 70 60" fill="none">
                        <defs>
                            <linearGradient id="logo_grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#2193b0;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#6dd5ed;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <path d="M25 10 L5 30 L25 50" stroke="url(#logo_grad)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M35 55 L50 5" stroke="url(#logo_grad)" stroke-width="6" stroke-linecap="round" />
                        <path d="M45 10 L65 30 L45 50" stroke="url(#logo_grad)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <h1 class="h3 mb-0 club-title">DEVCLUB MEMBER</h1>
                    <span class="text-muted small">System Management DevClub</span>
                </div>
            </div>

            <div class="col-md-6 mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
                <form action="index.php" method="GET" class="d-flex position-relative flex-grow-1 flex-md-grow-0" style="min-width: 300px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="search" name="search" class="form-control search-pill ps-5" placeholder="ค้นหา ID หรือ ชื่อ..." value="<?= htmlspecialchars($search) ?>">
                    <?php if (!empty($search)): ?>
                        <a href="index.php" class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"><i class="bi bi-x-circle-fill"></i></a>
                    <?php endif; ?>
                </form>

                <a href="create.php" class="btn btn-primary rounded-pill px-4 d-flex align-items-center shadow-sm" style="background: linear-gradient(45deg, #2193b0, #6dd5ed); border:none;">
                    <i class="bi bi-plus-lg me-2"></i> สมาชิก
                </a>
            </div>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                <div>
                    <strong>สำเร็จ!</strong> ลบข้อมูลสมาชิกออกจากระบบเรียบร้อยแล้ว
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="main-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th class="text-center">PROFILE</th>
                            <th>FULLNAME</th>
                            <th>CONTACT</th>
                            <th>MAJOR</th>
                            <th class="text-center">YEAR</th>
                            <th class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <?php
                                // จัดการรูปภาพ
                                $imgSrc = (!empty($row['profile_image']) && file_exists('uploads/' . $row['profile_image']))
                                    ? "uploads/{$row['profile_image']}"
                                    : "https://ui-avatars.com/api/?name=" . urlencode($row['fullname']) . "&background=random&color=fff"; // ใช้ Avatar อัตโนมัติถ้าไม่มีรูป

                                // Highlight คำค้นหา
                                $fullname_display = $row['fullname'];
                                if (!empty($search)) {
                                    $fullname_display = str_ireplace($search, "<span class='bg-warning bg-opacity-25 text-dark rounded px-1'>$search</span>", $row['fullname']);
                                }

                                // สีของสาขา
                                $badgeColor = getMajorColor($row['major']);
                                ?>
                                <tr class="animate-row">
                                    <td class="ps-4 text-muted fw-bold">#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></td>

                                    <td class="text-center">
                                        <img src="<?= $imgSrc ?>" class="member-thumb" alt="Profile">
                                    </td>

                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?= $fullname_display ?></div>
                                        <div class="small text-muted d-md-none"><?= $row['email'] ?></div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center text-secondary">
                                            <i class="bi bi-envelope me-2"></i> <?= $row['email'] ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill bg-<?= $badgeColor ?> bg-opacity-10 text-<?= $badgeColor ?> border border-<?= $badgeColor ?> border-opacity-25 px-3 py-2">
                                            <?= $row['major'] ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span class="fw-bold text-secondary"><?= $row['academic_year'] ?></span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="action-btn btn-outline-warning btn-edit border-0 bg-warning bg-opacity-10 text-warning" title="แก้ไข">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="action-btn btn-outline-danger btn-delete border-0 bg-danger bg-opacity-10 text-danger" onclick="return confirm('ยืนยันที่จะลบคุณ <?= $row['fullname'] ?> ?');" title="ลบ">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-50">
                                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                                        <span class="fs-5">ไม่พบข้อมูลที่ค้นหา...</span>
                                        <p class="small mt-2">ลองใช้คำค้นหาอื่นดูนะครับ</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 text-muted opacity-50 small">
            Designed for <strong class="text-dark">DevClub</strong> © <?= date('Y') ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>