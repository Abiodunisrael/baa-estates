<?php
/**
 * Flash messages stored in the session.
 */

function flash_set(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_get(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function flash_success(string $msg): void { flash_set('success', $msg); }
function flash_error(string $msg): void   { flash_set('error', $msg); }
function flash_info(string $msg): void    { flash_set('info', $msg); }