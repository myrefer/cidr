<?php

namespace CIDR;

class IPv4 {
  static function addrToInt($addr) {
    if (!is_string($addr)) {
      return [0, true];
    }

    $int = ip2long($addr);

    if ($int === false) {
      return [0, true];
    }

    return [$int, null];
  }

  static function netmask($i) {
    if (!is_int($i)) {
      return [0, true];
    }

    if ($i < 0 || $i > 32) {
      return [0, true];
    }

    $netmask = pow(2, $i)-1 << (32-$i);

    return [$netmask, null];
  }

  static function match($haystack, $needle) {
    $a = explode('/', $haystack);

    $haystack_addr = $a[0];
    $haystack_netmask = count($a) > 1 ? $a[1] : null;

    list($_haystack_addr, $err) = self::addrToInt($haystack_addr);
    if ($err !== null) {
      return [null, $err];
    }

    list($_needle, $err) = self::addrToInt($needle);
    if ($err !== null) {
      return [null, $err];
    }

    if ($haystack_netmask === null) {

      return [$_haystack_addr === $_needle, null];

    } else {

      // Make sure string is valid int
      $haystack_netmask_i = (int)$haystack_netmask;
      if ($haystack_netmask !== (string)$haystack_netmask_i) {
        return [null, true];
      }

      list($_haystack_netmask, $err) = self::netmask($haystack_netmask_i);
      if ($err !== null) {
        return [null, $err];
      }

      $haystack_masked = $_haystack_addr & $_haystack_netmask;
      $needle_masked = $_needle & $_haystack_netmask;

      $match = $haystack_masked === $needle_masked;

      return [$match, null];
    }
  }
}
