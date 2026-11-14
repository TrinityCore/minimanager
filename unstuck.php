<?php
require_once 'header.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['realm_id'])) {
    redirect('login.php');
}

$guid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$account_id = (int)$_SESSION['user_id'];
$realm_id = (int)$_SESSION['realm_id'];

if ($guid <= 0) {
    redirect('index.php');
}

// Connect to the active realm's characters database using MiniManager's config array
$char_db = $characters_db[$realm_id];
$sqlc = new mysqli($char_db['addr'], $char_db['user'], $char_db['pass'], $char_db['name']);

if ($sqlc->connect_error) {
    die('Database connection failed.');
}

// 1. Verify the character belongs to the logged-in account and is offline
$stmt = $sqlc->prepare('SELECT online FROM characters WHERE guid = ? AND account = ? LIMIT 1');
$stmt->bind_param('ii', $guid, $account_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    if ((int)$row['online'] === 1) {
        // Character must be offline to avoid worldserver overwriting coordinates
        $stmt->close();
        $sqlc->close();
        redirect('char.php?id=' . $guid . '&error=online');
    }
    $stmt->close();

    // 2. Get the character's Hearthstone (homebind) location
    $hb_stmt = $sqlc->prepare('SELECT mapId, zoneId, posX, posY, posZ FROM character_homebind WHERE guid = ? LIMIT 1');
    $hb_stmt->bind_param('i', $guid);
    $hb_stmt->execute();
    $hb_res = $hb_stmt->get_result();

    if ($hb = $hb_res->fetch_assoc()) {
        $hb_stmt->close();

        // 3. Teleport character to homebind
        $upd_stmt = $sqlc->prepare(
            'UPDATE characters 
             SET position_x = ?, position_y = ?, position_z = ?, map = ?, zone = ? 
             WHERE guid = ? AND account = ? AND online = 0'
        );
        $upd_stmt->bind_param(
            'dddiiii',
            $hb['posX'],
            $hb['posY'],
            $hb['posZ'],
            $hb['mapId'],
            $hb['zoneId'],
            $guid,
            $account_id
        );
        $upd_stmt->execute();
        $upd_stmt->close();
    } else {
        $hb_stmt->close();
    }
} else {
    $stmt->close();
}

$sqlc->close();

// Redirect back to the referring page or character sheet
$return_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'char.php?id=' . $guid;
header('Location: ' . $return_url);
exit;

