<?php
/**
 * Floating WhatsApp button.
 * Renders only if whatsapp_enabled = 1 and whatsapp_number is set.
 */

$wa_enabled = setting('whatsapp_enabled', '0');
$wa_number  = preg_replace('/\D/', '', setting('whatsapp_number', ''));
$wa_message = setting('whatsapp_message', 'Hello, I would like to make an enquiry.');

if ($wa_enabled !== '1' || $wa_number === '') {
    return;
}

$wa_url = 'https://wa.me/' . $wa_number . '?text=' . rawurlencode($wa_message);
?>
<a href="<?= e($wa_url) ?>"
   class="whatsapp-float"
   target="_blank"
   rel="noopener"
   aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp" aria-hidden="true"></i>
    <span class="whatsapp-float-text">Chat with us</span>
</a>