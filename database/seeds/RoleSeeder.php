<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Str;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //Role Admin  1
         $role=new Role();
         $role->name="admin";
         $role->slug=Str::slug("admin");
         $role->save();
      
         //Role User  2
         $role=new Role();
         $role->name="user";
         $role->slug=Str::slug("user");
         $role->save();
      
         //Role Provider 3
         $role=new Role();
         $role->name="provider";
         $role->slug=Str::slug("provider");
         $role->save();

        // Role Organization 4
         $role=new Role();
         $role->name="organization";
         $role->slug=Str::slug("organization");
         $role->save();

          // Role Organization 5
          $role=new Role();
          $role->name="provider_user";
          $role->slug=Str::slug("provider_user");
          $role->save();

        // Role Organization 6
         $role=new Role();
         $role->name="organization_user";
         $role->slug=Str::slug("organization_user");
         $role->save();
    }
}
