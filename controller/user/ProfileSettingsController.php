<?php
require("config/env.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $currentPassword = $_POST['currentPassword'] ?? null;
    $newPassword = $_POST['newPassword'] ?? null;
    $newConfirmPassword = $_POST['newconfirmPassword'] ?? null;

    if (empty($id) || empty($currentPassword) || empty($newPassword) || empty($newConfirmPassword)) {
        echo "3";
        exit();
    }
    if ($newPassword !== $newConfirmPassword) {
        echo "4";
        exit();
    }
    $UserInfo = $h->table('users')->select()->where('id', '=', $id)->fetchAll();
    if (!password_verify($currentPassword, $UserInfo[0]['password'])) {
        echo "2";
        exit();
    }
    try {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $h->update('users')->values([
            'password' => $hashed_password,
        ])->where('id', '=', $id)->run();

        echo "1";
    } catch (PDOException $e) {
        echo "0";
    }
    exit();
}



