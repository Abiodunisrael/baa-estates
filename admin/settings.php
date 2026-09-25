<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'settings';
$admin_title = 'Site Settings';

$fields = [
    // General
    'site_name'        => ['label' => 'Site Name',        'type' => 'text'],
    'site_tagline'     => ['label' => 'Tagline',          'type' => 'text'],
    // Contact
    'site_email'       => ['label' => 'Contact Email',    'type' => 'email'],
    'site_phone'       => ['label' => 'Contact Phone',    'type' => 'text'],
    'site_address'     => ['label' => 'Office Address',   'type' => 'textarea'],
    // Social
    'social_facebook'  => ['label' => 'Facebook URL',     'type' => 'url'],
    'social_instagram' => ['label' => 'Instagram URL',    'type' => 'url'],
    'social_twitter'   => ['label' => 'X / Twitter URL',  'type' => 'url'],
    'social_linkedin'  => ['label' => 'LinkedIn URL',     'type' => 'url'],
    // WhatsApp
    'whatsapp_enabled' => ['label' => 'WhatsApp Enabled', 'type' => 'checkbox'],
    'whatsapp_number'  => ['label' => 'WhatsApp Number',  'type' => 'text'],
    'whatsapp_message' => ['label' => 'WhatsApp Greeting','type' => 'text'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $key => $meta) {
        if ($meta['type'] === 'checkbox') {
            $val = !empty($_POST[$key]) ? '1' : '0';
        } else {
            $val = trim($_POST[$key] ?? '');
        }
        setting_set($key, $val);
    }
    flash_success('Settings updated.');
    redirect(url('admin/settings.php'));
}

$current = settings_all();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width:720px;">
    <h2>Site Settings</h2>
    <p style="color:#6b6b6b; font-size:.88rem; margin-bottom:1rem;">
        These values appear across the public site — header, footer, contact page.
    </p>

    <form method="post" class="admin-form">

        <!-- ============ GENERAL ============ -->
        <h3 style="font-family:var(--font-body); font-size:.9rem; color:#6b6b6b; text-transform:uppercase; letter-spacing:.05em; margin:.5rem 0 0;">
            General
        </h3>
        <div class="form-row">
            <div>
                <label><?= e($fields['site_name']['label']) ?></label>
                <input type="text" name="site_name" value="<?= e($current['site_name'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['site_tagline']['label']) ?></label>
                <input type="text" name="site_tagline" value="<?= e($current['site_tagline'] ?? '') ?>">
            </div>
        </div>

        <!-- ============ CONTACT ============ -->
        <h3 style="font-family:var(--font-body); font-size:.9rem; color:#6b6b6b; text-transform:uppercase; letter-spacing:.05em; margin:1rem 0 0;">
            Contact
        </h3>
        <div class="form-row cols-2">
            <div>
                <label><?= e($fields['site_email']['label']) ?></label>
                <input type="email" name="site_email" value="<?= e($current['site_email'] ?? '') ?>">
            </div>
            <div>
                <label><?= e($fields['site_phone']['label']) ?></label>
                <input type="text" name="site_phone" value="<?= e($current['site_phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['site_address']['label']) ?></label>
                <textarea name="site_address" rows="2"><?= e($current['site_address'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ============ SOCIAL LINKS ============ -->
        <h3 style="font-family:var(--font-body); font-size:.9rem; color:#6b6b6b; text-transform:uppercase; letter-spacing:.05em; margin:1rem 0 0;">
            Social Links
        </h3>
        <div class="form-row">
            <div>
                <label><?= e($fields['social_facebook']['label']) ?></label>
                <input type="url" name="social_facebook"
                       value="<?= e($current['social_facebook'] ?? '') ?>"
                       placeholder="https://facebook.com/...">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['social_instagram']['label']) ?></label>
                <input type="url" name="social_instagram"
                       value="<?= e($current['social_instagram'] ?? '') ?>"
                       placeholder="https://instagram.com/...">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['social_twitter']['label']) ?></label>
                <input type="url" name="social_twitter"
                       value="<?= e($current['social_twitter'] ?? '') ?>"
                       placeholder="https://x.com/...">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['social_linkedin']['label']) ?></label>
                <input type="url" name="social_linkedin"
                       value="<?= e($current['social_linkedin'] ?? '') ?>"
                       placeholder="https://linkedin.com/...">
            </div>
        </div>

        <!-- ============ WHATSAPP ============ -->
        <h3 style="font-family:var(--font-body); font-size:.9rem; color:#6b6b6b; text-transform:uppercase; letter-spacing:.05em; margin:1rem 0 0;">
            WhatsApp Button
        </h3>
        <div class="form-row">
            <div class="checkbox-row">
                <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" value="1"
                       <?= ($current['whatsapp_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                <label for="whatsapp_enabled" style="margin:0;">Show floating WhatsApp button on the public site</label>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['whatsapp_number']['label']) ?></label>
                <input type="text" name="whatsapp_number"
                       value="<?= e($current['whatsapp_number'] ?? '') ?>"
                       placeholder="2348012345678">
                <div class="form-help">International format, no + or spaces. Example: <code>2348012345678</code> for Nigeria.</div>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label><?= e($fields['whatsapp_message']['label']) ?></label>
                <input type="text" name="whatsapp_message"
                       value="<?= e($current['whatsapp_message'] ?? '') ?>"
                       placeholder="Hello, I would like to make an enquiry.">
                <div class="form-help">This text appears in the user's WhatsApp chat when they tap the button.</div>
            </div>
        </div>

        <!-- ============ SUBMIT ============ -->
        <div class="form-actions">
            <button type="submit" class="btn-admin-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>

    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>