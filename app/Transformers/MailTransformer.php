<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 02:31
 * IDE: PhpStorm
 */

namespace App\Transformers;

use App\Mail;
use League\Fractal\TransformerAbstract;

class MailTransformer extends TransformerAbstract
{
    public function transform(Mail $mail)
    {
        return $mail->toArray();
    }
}
