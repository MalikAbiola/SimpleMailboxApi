<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 02:24
 * IDE: PhpStorm
 */

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogUtils
{
    public function logError(\Exception $e)
    {
        Log::error(
            $e->getMessage(),
            [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        );
    }
}
