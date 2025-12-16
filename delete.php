<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. ดึงข้อมูลสมาชิกก่อนเพื่อเอาชื่อไฟล์รูปภาพ
    $stmtFetch = $conn->prepare("SELECT profile_image FROM members WHERE id = ?");
    $stmtFetch->execute([$id]);
    $member = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        // 2. ลบไฟล์รูปภาพออกจากโฟลเดอร์ uploads (ถ้ามี)
        $imagePath = 'uploads/' . $member['profile_image'];
        if (!empty($member['profile_image']) && file_exists($imagePath)) {
            unlink($imagePath); // สั่งลบไฟล์
        }

        // 3. ลบข้อมูลในฐานข้อมูล
        $stmtDelete = $conn->prepare("DELETE FROM members WHERE id = ?");
        $stmtDelete->execute([$id]);
    }
}

header("Location: index.php?status=deleted");
exit();
