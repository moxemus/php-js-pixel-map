<?php

namespace src\components;

use src\config\DB;

class Handler
{
    const TIME_LIMIT = 5 * 60;

    public function updateMapCell(string $ip, string $color, int $rowIndex, int $colIndex): bool
    {
        $time = time();
        $updated = DB::queryOne("select updated from users where ipAddress = '$ip'");

        if (!$updated) {
            DB::exec("insert into users (ipAddress, updated) values('$ip', $time)");
        } elseif (($updated + self::TIME_LIMIT) <= $time) {
            DB::exec("update users set updated = $time where ipAddress = '$ip'");
        } else {
            return false;
        }

        DB::exec("update map set color = '$color' where rowIndex = $rowIndex and colIndex = $colIndex");


        return true;
    }

    public function getMap(): array
    {
        return DB::query("select rowIndex, colIndex, color from map");
    }
}