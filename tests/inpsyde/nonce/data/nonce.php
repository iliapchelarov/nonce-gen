<?php
use inpsyde\nonce\Nonce;
use inpsyde\nonce\NonceFixture;

$code = 1298371928;
$key = 'zslmv;ldajf[voueldzn!_@_$(*&#_98275-9840vudgfhAS}PAOCNUREGFIUEWQOIHKJBDEV';
$time = time() + 1;

return array (
    'initial' => [
        Nonce::CODE => $code,
        Nonce::KEY  => $key,
        Nonce::TIME => $time,
        NonceFixture::VALID => true,
    ],
    'nocode' => [
        Nonce::CODE => null,
        Nonce::KEY  => $key,
        Nonce::TIME => $time,
        NonceFixture::VALID => false,
    ],
    'nokey' => [
        Nonce::CODE => $code + 1,
        Nonce::KEY  => null,
        Nonce::TIME => $time,
        NonceFixture::VALID => false,
    ],
    'notime' => [
        Nonce::CODE => $code + 2,
        Nonce::KEY  => $key,
        Nonce::TIME => null,
        NonceFixture::VALID => true,
    ],
    'expired' => [
        Nonce::CODE => $code + 3,
        Nonce::KEY  => $key,
        Nonce::TIME => 12345678,
        NonceFixture::VALID => false,
    ],
);
