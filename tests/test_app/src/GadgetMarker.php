<?php
declare(strict_types=1);

namespace TestApp;

class GadgetMarker
{
    public static bool $woken = false;

    public function __wakeup(): void
    {
        self::$woken = true;
        echo "  [!] GadgetMarker::__wakeup fired during unserialize (object injection)\n";
    }
}
