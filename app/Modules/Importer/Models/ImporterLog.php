<?php

namespace App\Modules\Importer\Models;

use App\Core\LogModel;
use Illuminate\Database\Eloquent\Model;

class ImporterLog extends Model
{


    protected $fillable = [

        'id',
        'type',
        'run_at',
        'entries_processed',
        'entries_created'
    ];



    // relationships

    // scopes

    // getters
}
