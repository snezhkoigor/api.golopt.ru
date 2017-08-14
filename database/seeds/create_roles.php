<?php

use Illuminate\Database\Seeder;

class create_roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = new \Kodeine\Acl\Models\Eloquent\Role();
        $roleAdmin->name = 'Administrator';
        $roleAdmin->slug = 'administrator';
        $roleAdmin->description = 'Super user';
        $roleAdmin->save();

        $clientAdmin = new \Kodeine\Acl\Models\Eloquent\Role();
        $clientAdmin->name = 'Client';
        $clientAdmin->slug = 'client';
        $clientAdmin->description = 'Website client';
        $clientAdmin->save();
    }
}
