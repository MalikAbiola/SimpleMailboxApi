<?php
/**
 * Created by Malik Abiola.
 * Date: 09/11/2016
 * Time: 04:46
 * IDE: PhpStorm
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender',
        'subject',
        'message',
        'read',
        'archived',
    ];

    protected $casts = [
        'read' => 'boolean',
        'archived' => 'boolean',
        'time_sent' => 'datetime'
    ];

    protected $primaryKey = 'uid';
}
