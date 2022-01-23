<?php

namespace App\Modules\Importer\Models;

use App\Core\LogModel;
use Illuminate\Database\Eloquent\Model;

class Importer extends Model
{


    protected $fillable = [

        'work_order_number',
        'category',
        'fin_loc',
        'priority',
        'received_date',
        'external_id'
    ];



    // relationships

    // scopes

    // getters
}
