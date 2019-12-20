<?php


namespace App\Security;


class TokenGenerator
{
    /**
     * Generate a random token
     * @return string
     */
    public function generateToken()
    {
        try {
            return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'),
                '=');
        } catch (\Exception $e) {
        }
    }
}