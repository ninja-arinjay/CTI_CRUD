<?php
// Helper functions
require_once "config.php";

function encrypt_url($id) {
    return base64_encode(openssl_encrypt($id, 'aes-128-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_KEY));
}

function decrypt_url($encrypted_id) {
    return openssl_decrypt(base64_decode($encrypted_id), 'aes-128-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_KEY);
}

?>
