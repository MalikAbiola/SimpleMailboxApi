<?php
/**
 * Created by Malik Abiola.
 * Date: 09/11/2016
 * Time: 04:46
 * IDE: PhpStorm
 */

namespace App;

use Carbon\Carbon;
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

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'read' => 'boolean',
        'archived' => 'boolean',
    ];

    protected $primaryKey = 'uid';

    /**
     * Convert time_sent column to data time string during retrieval.
     * @param $value
     * @return string
     */
    public function getTimeSentAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }
}
