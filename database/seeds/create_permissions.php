<?php

use Illuminate\Database\Seeder;

class create_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = new \Kodeine\Acl\Models\Eloquent\Permission();
        $permClient = $permission->create([
            'name'        => 'option.profit',
            'slug'        => [          // pass an array of permissions.
                'real' => true,
                'demo' => true,
                'odr' => true,
                'lite' => true,
                'full' => true,
                'incremental.outflow' => true
            ],
            'description' => 'access for Option Profit indicator'
        ]);
    }
}