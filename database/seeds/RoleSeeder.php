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
         //Role Admin  
         $role=new Role();
         $role->name="admin";
         $role->slug=Str::slug("admin");
         $role->save();
      
         //Role User  
         $role=new Role();
         $role->name="user";
         $role->slug=Str::slug("user");
         $role->save();
      
         //Role Provider
         $role=new Role();
         $role->name="provider";
         $role->slug=Str::slug("provider");
         $role->save();

        // Role Organization 
         $role=new Role();
         $role->name="organization";
         $role->slug=Str::slug("organization");
         $role->save();

          // Role Organization 
          $role=new Role();
          $role->name="provider_user";
          $role->slug=Str::slug("provider_user");
          $role->save();

        // Role Organization 
         $role=new Role();
         $role->name="organization_user";
         $role->slug=Str::slug("organization_user");
         $role->save();
    }
}
