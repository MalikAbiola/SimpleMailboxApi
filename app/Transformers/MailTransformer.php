<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 02:31
 * IDE: PhpStorm
 */

namespace App\Transformers;

use App\Mail;
use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

class MailTransformer extends TransformerAbstract
{
    protected $showArchiveStatus;

    public function __construct($showArchiveStatus)
    {
        $this->showArchiveStatus = $showArchiveStatus;
    }

    /**
     * Format Mail representation in responses
     * @param Mail $mail
     * @return array
     */
    public function transform(Mail $mail)
    {
        return $this->showArchiveStatus ? $mail->toArray() : Arr::except($mail->toArray(), ['archived']);
    }
}
