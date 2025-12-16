<?php
// ไฟล์: fetch_members.php
require_once 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM members";
$params = [];

if (!empty($search)) {
    $sql .= " WHERE id = ? OR fullname LIKE ?";
    $params[] = $search;
    $params[] = "%$search%";
}

$sql .= " ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

// ฟังก์ชันสุ่มสี (ก๊อปปี้มาเพื่อให้สีตรงกัน)
function getMajorColor($major)
{
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
    $index = crc32($major) % count($colors);
    return $colors[$index];
}

// --- เริ่มสร้าง HTML ของแถวตาราง ---
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // จัดการรูปภาพ
        $imgSrc = (!empty($row['profile_image']) && file_exists('uploads/' . $row['profile_image']))
            ? "uploads/{$row['profile_image']}"
            : "https://ui-avatars.com/api/?name=" . urlencode($row['fullname']) . "&background=random&color=fff";

        // Highlight คำค้นหา
        $fullname_display = $row['fullname'];
        if (!empty($search)) {
            $fullname_display = str_ireplace($search, "<span class='bg-warning bg-opacity-25 text-dark rounded px-1'>$search</span>", $row['fullname']);
        }

        $badgeColor = getMajorColor($row['major']);

        echo '<tr class="animate-row">';
        echo '<td class="ps-4 text-muted fw-bold">#' . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . '</td>';
        echo '<td class="text-center"><img src="' . $imgSrc . '" class="member-thumb" alt="Profile"></td>';
        echo '<td>';
        echo '<div class="fw-bold text-dark fs-6">' . $fullname_display . '</div>';
        echo '<div class="small text-muted d-md-none">' . $row['email'] . '</div>';
        echo '</td>';
        echo '<td><div class="d-flex align-items-center text-secondary"><i class="bi bi-envelope me-2"></i> ' . $row['email'] . '</div></td>';
        echo '<td><span class="badge rounded-pill bg-' . $badgeColor . ' bg-opacity-10 text-' . $badgeColor . ' border border-' . $badgeColor . ' border-opacity-25 px-3 py-2">' . $row['major'] . '</span></td>';
        echo '<td class="text-center"><span class="fw-bold text-secondary">' . $row['academic_year'] . '</span></td>';
        echo '<td class="text-center">';
        echo '<div class="d-flex justify-content-center gap-2">';
        echo '<a href="edit.php?id=' . $row['id'] . '" class="action-btn btn-outline-warning btn-edit border-0 bg-warning bg-opacity-10 text-warning"><i class="bi bi-pencil-fill"></i></a>';
        echo '<a href="delete.php?id=' . $row['id'] . '" class="action-btn btn-outline-danger btn-delete border-0 bg-danger bg-opacity-10 text-danger" onclick="return confirm(\'ยืนยันที่จะลบคุณ ' . $row['fullname'] . ' ?\');"><i class="bi bi-trash-fill"></i></a>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    // กรณีไม่เจอข้อมูล
    echo '<tr><td colspan="7" class="text-center py-5"><div class="opacity-50"><i class="bi bi-search fs-1 d-block mb-3"></i><span class="fs-5">ไม่พบข้อมูล...</span></div></td></tr>';
}
