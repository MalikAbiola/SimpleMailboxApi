<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 20:29
 * IDE: PhpStorm
 */

namespace App\Validators\Rules;

class Mails
{
    public static function getRules()
    {
        return [
            'read'      => 'sometimes|required|boolean',
            'archive'   => 'sometimes|required|boolean'
        ];
    }

    public static function getMessages()
    {
        return [
            'read.required'     => 'The read value must be provided',
            'read.boolean'      => 'The read value must be either true or false',
            'archive.required'  => 'The archive value must be provided',
            'archive.boolean'   => 'The archive value must be either true or false'
        ];
    }
}
