<?php

namespace Dxw\CIDR;

class AddressBase
{
    private $address;
    protected static $unpackSize;

    public static function Make(string $address): \Dxw\Result\Result
    {
        // I hate to suppress warnings, but there's no other way to disable
        // warnings generated by inet_pton, and we are handling it
        $_address = @inet_pton($address);
        if ($_address === false) {
            return \Dxw\Result\Result::err('inet_pton error: unrecognised address');
        }

        return \Dxw\Result\Result::ok(new static($_address));
    }

    private function __construct(string $address)
    {
        $this->address = $address;
    }

    public function __toString(): string
    {
        return inet_ntop($this->address);
    }

    public function getBinary(): \GMP
    {
        return $this->inAddrToGmp($this->address);
    }

    private function inAddrToGmp(string $in_addr): \GMP
    {
        $unpacked = unpack('a'.static::$unpackSize, $in_addr);
        $unpacked = str_split($unpacked[1]);
        $binary = '';
        foreach ($unpacked as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return gmp_init($binary, 2);
    }
}