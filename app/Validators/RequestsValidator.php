<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 20:28
 * IDE: PhpStorm
 */

namespace App\Validators;

use App\Validators\Rules\Mails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestsValidator
{
    public static function validateMailUpdateRequest(Request $request)
    {
        return Validator::make(
            $request->all(),
            Mails::getRules(),
            Mails::getMessages());
    }
}
