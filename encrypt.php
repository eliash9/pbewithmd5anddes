<?php
error_reporting(0);
$content = trim(file_get_contents("php://input"));

$decoded = json_decode($content, true);
$cRekening 		= $decoded['Rekening'];

function hexToBinSafe($hex) {
    return pack("H*", $hex);
}

function pbeWithMD5AndDES_Encrypt($data, $password, $saltHex, $iterations = 20) {
    $salt = hexToBinSafe($saltHex);
    
    // PBKDF1 MD5 Implementation
    $hash = md5($password . $salt, true);
    for ($i = 1; $i < $iterations; $i++) {
        $hash = md5($hash, true);
    }
    
    $key = substr($hash, 0, 8); // DES key
    $iv = substr($hash, 8, 8);  // IV
    
    // Add PKCS5 padding manually
    $blockSize = 8; // DES block size is 8 bytes
    $pad = $blockSize - (strlen($data) % $blockSize);
    $data .= str_repeat(chr($pad), $pad);
    
    // Encrypt using DES-CBC with manual padding
    $ciphertext = openssl_encrypt($data, 'DES-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
    
    return base64_encode($ciphertext);
}

// Input data
$password = "secret";
$salt = "de331012de331012"; // Hex format
$plainText = "hello world";

// Hasil
$encrypted = pbeWithMD5AndDES_Encrypt($cRekening , $password, $salt);
//echo "Encrypted (Base64): " . $encrypted;
echo json_encode($encrypted);