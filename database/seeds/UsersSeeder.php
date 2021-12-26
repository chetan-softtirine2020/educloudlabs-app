<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user= new User();
        $user->first_name = "Educloud";
        $user->last_name = "Admin";
        $user->slug=Str::slug("Educloud Admin");
        $user->mobile_no = "9960491861";
        $user->password = bcrypt("Password@123");
        $user->email = "admin@educloudlabs.com";
        $user->role = 1;
        $user->save();

        $user= new User();
        $user->first_name = "Normal";
        $user->last_name = "User";
        $user->slug=Str::slug("Normal User");
        $user->mobile_no = "9860491861";
        $user->password = bcrypt("Password@123");
        $user->email = "user@test.com";
        $user->role = "2";
        $user->save();
              
    }
}
