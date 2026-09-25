<?php
/**
 * Global helper functions.
 */

/** Escape output safely for HTML. */
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redirect and stop execution. */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/** Build a URL relative to SITE_URL. */
function url(string $path = ''): string {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/** Turn a string into a URL-safe slug. */
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/** Format a price with Naira and optional period. */
function format_price(float $price, ?string $period = null): string {
    $formatted = '₦' . number_format($price, 0);
    if ($period && $period !== 'total') {
        $formatted .= ' / ' . $period;
    }
    return $formatted;
}

/** Human-readable property type. */
function property_type_label(string $type): string {
    return $type === 'house' ? 'House' : 'Land';
}

/** Human-readable listing type. */
function listing_type_label(string $type): string {
    return $type === 'sale' ? 'For Sale' : 'For Lease';
}

/** Return the primary image URL for a property, or a placeholder. */
function property_image_url(?string $filename): string {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . $filename;
    }
    return url('assets/images/placeholder.jpg');
}

/** Truncate a string safely. */
function excerpt(string $text, int $length = 120): string {
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length - 1) . '…';
}

/** Get status badge class. */
function status_badge_class(string $status): string {
    return match ($status) {
        'available' => 'badge-success',
        'sold'      => 'badge-danger',
        'leased'    => 'badge-warning',
        'pending'   => 'badge-muted',
        default     => 'badge-muted',
    };
}

/* ============================================================
   SETTINGS
   ============================================================ */

/**
 * Load all settings into a cached array.
 * Uses a static so we only query once per request.
 */
function settings_all(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    global $pdo;
    $cache = [];
    try {
        $rows = $pdo->query("SELECT `key`, `value` FROM settings")->fetchAll();
        foreach ($rows as $r) {
            $cache[$r['key']] = $r['value'];
        }
    } catch (Throwable $e) {
        // Table might not exist yet — silently fall back
    }
    return $cache;
}

/** Get a single setting, with fallback default. */
function setting(string $key, string $default = ''): string {
    $all = settings_all();
    $val = $all[$key] ?? null;
    return ($val === null || $val === '') ? $default : (string)$val;
}

/** Update one setting (upsert). */
function setting_set(string $key, string $value): void {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO settings (`key`, `value`) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
    ");
    $stmt->execute([$key, $value]);
}

/** Site info helpers — read from DB, fall back to defaults. */
function site_name(): string    { return setting('site_name',    SITE_NAME_DEFAULT); }
function site_tagline(): string { return setting('site_tagline', SITE_TAGLINE_DEFAULT); }
function site_email(): string   { return setting('site_email',   SITE_EMAIL_DEFAULT); }
function site_phone(): string   { return setting('site_phone',   SITE_PHONE_DEFAULT); }
function site_address(): string { return setting('site_address', SITE_ADDRESS_DEFAULT); }