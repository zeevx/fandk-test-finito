<?php
namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Str;

class GenerateNumberHelper{

    public static function generateAccount()
    {
        $no = (string) random_int(00000000, 99999999);
        $no = "17$no";

        if ( strlen($no) != 10 ) {
            return self::generateAccount();
        }

        if ( User::where('number', $no)->count() > 0 ) {
            return self::generateAccount();
        }

        return $no;
    }

}
