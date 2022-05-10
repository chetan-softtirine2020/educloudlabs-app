<?php

use App\Models\LPTraining;
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
        $user = new User();
        $user->first_name = "Educloud";
        $user->last_name = "Admin";
        $user->slug = Str::slug("Educloud Admin");
        $user->mobile_no = "9960491861";
        $user->password = bcrypt("Password@123");
        $user->email = "admin@educloudlabs.com";
        $user->role = 1;
        $user->save();

        $user = new User();
        $user->first_name = "Normal";
        $user->last_name = "User";
        $user->slug = Str::slug("Normal User");
        $user->mobile_no = "9860491861";
        $user->password = bcrypt("Password@123");
        $user->email = "user@test.com";
        $user->role = "2";
        $user->save();

        $userlp = new User();
        $userlp->first_name = "Demo";
        $userlp->last_name = "Provider";
        $userlp->slug = Str::slug("Demo Provider");
        $userlp->mobile_no = "9999955522";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lp@educloudlabs.com";
        $userlp->role = 3;
        $userlp->save();

        $userlp = new User();
        $userlp->first_name = "Demo";
        $userlp->last_name = "Provider";
        $userlp->slug = Str::slug("Demo Provider");
        $userlp->mobile_no = "9999955522";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lp@educloudlabs.com";
        $userlp->role = 3;
        $userlp->save();

        $userlp = new User();
        $userlp->first_name = "Demo LPUser1";
        $userlp->last_name = "User1";
        $userlp->slug = Str::slug("Demo LPUser1");
        $userlp->mobile_no = "9999955533";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lpuser1@educloudlabs.com";
        $userlp->role = 3;
        $userlp->parent_id =  $userlp->id;
        $userlp->save();

        $userlp = new User();
        $userlp->first_name = "Demo LPUser2";
        $userlp->last_name = "User2";
        $userlp->slug = Str::slug("Demo LPUser2");
        $userlp->mobile_no = "9999955544";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lpuser2@educloudlabs.com";
        $userlp->role = 3;
        $userlp->parent_id =  $userlp->id;
        $userlp->save();

        $userlp = new User();
        $userlp->first_name = "Demo LPUser3";
        $userlp->last_name = "User3";
        $userlp->slug = Str::slug("Demo LPUser3");
        $userlp->mobile_no = "999995566";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lpuser3@educloudlabs.com";
        $userlp->role = 3;
        $userlp->parent_id =  $userlp->id;
        $userlp->save();

        $userlp = new User();
        $userlp->first_name = "Demo LPUser4";
        $userlp->last_name = "User4";
        $userlp->slug = Str::slug("Demo LPUser4");
        $userlp->mobile_no = "999995577";
        $userlp->password = bcrypt("demolp@123");
        $userlp->email = "demo-lpuser4@educloudlabs.com";
        $userlp->role = 3;
        $userlp->parent_id =  $userlp->id;
        $userlp->save();
       
    }
}
