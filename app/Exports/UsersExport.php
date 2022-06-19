<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;

class UsersExport implements FromQuery
{
    public function query()
    {
        return User::query();
    }
}
