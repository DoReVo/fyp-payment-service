<?php
namespace App\Helpers;

use Firebase\JWT\JWT;

class JWTHelper
{
    private $privateKey;
    private $publicKey;
    private $algo;

    public function __construct()
    {
        $this->privateKey = getenv('JWT_PRIVATE_KEY');
        $this->publicKey = getenv('JWT_PUBLIC_KEY');
        $this->algo = 'RS256';
    }

    public function encode($claim)
    {
        $jwtHandler = new JWT;
        return $jwtHandler->encode($claim, $this->privateKey, $this->algo);
    }

    public function decode($token)
    {
        $jwtHandler = new JWT;
        return (array) $jwtHandler->decode($token, $this->publicKey, array($this->algo));
    }
}
