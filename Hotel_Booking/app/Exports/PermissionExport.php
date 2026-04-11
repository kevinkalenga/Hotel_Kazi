<?php

namespace App\Exports;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;

class PermissionExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //  return Permission::select('name','group_name', 'guard_name', 'created_at')->get();
        return Permission::select(
            'name',
            'group_name',
            'guard_name',
            'created_at'
        )->get()->map(function ($item) {
            return [
                'name'       => $item->name,
                'group_name' => $item->group_name,
                'guard_name' => $item->guard_name,
                'created_at' => $item->created_at->format('d/m/Y H:i'),
            ];
        });
    }
}
