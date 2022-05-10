<?php

use App\Models\LPTraining;
use App\Models\LPTUser;
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




        $userlp1 = new User();
        $userlp1->first_name = "DemoLP1";
        $userlp1->last_name = "Provider";
        $userlp1->slug = Str::slug("DemoLP1 Provider1");
        $userlp1->mobile_no = "9999955522";
        $userlp1->password = bcrypt("demolp@123");
        $userlp1->email = "demolp1@educloudlabs.com";
        $userlp1->role = 3;
        $userlp1->save();


        $userlp2 = new User();
        $userlp2->first_name = "DemoLP2";
        $userlp2->last_name = "Provider";
        $userlp2->slug = Str::slug("DemoLP2 Provider2");
        $userlp2->mobile_no = "9999955522";
        $userlp2->password = bcrypt("demolp@123");
        $userlp2->email = "demolp2@educloudlabs.com";
        $userlp2->role = 3;
        $userlp2->save();

        /* Create Traning*/
        $training1 = new LPTraining();
        $training1->name = 'Python (Basic)';
        $training1->slug = Str::slug('Python (Basic)');
        $training1->date = date('Y-m-d h:i:s');
        $training1->link =  'Python (Basic)';
        $training1->description = "This Python basic training imporve basic skill using this training";
        $training1->user_id = $userlp1->id;
        $training1->is_paid = 0;
        $training1->created_by = $userlp1->id;
        $training1->save();

        $training2 = new LPTraining();
        $training2->name = 'Python (Intermediate)';
        $training2->slug = Str::slug('Python (Intermediate)');
        $training2->date = date('Y-m-d h:i:s');
        $training2->link =  'Python (Intermediate)';
        $training2->description = "This Python Intermediate training imporve basic skill using this training";
        $training2->user_id = $userlp1->id;
        $training2->is_paid = 0;
        $training2->created_by = $userlp1->id;
        $training2->save();

        $training3 = new LPTraining();
        $training3->name = 'Python (Advance)';
        $training3->slug = Str::slug('Python (Advance)');
        $training3->date = date('Y-m-d h:i:s');
        $training3->link =  'Python (Advance)';
        $training3->description = "This Python Advance training imporve basic skill using this training";
        $training3->user_id = $userlp1->id;
        $training3->is_paid = 0;
        $training3->created_by = $userlp1->id;
        $training3->save();


        $training4 = new LPTraining();
        $training4->name = 'C Programining (Basic)';
        $training4->slug = Str::slug('Python (Basic)');
        $training4->date = date('Y-m-d h:i:s');
        $training4->link =  'C Programining (Basic)';
        $training4->description = "This C Programining Basic training imporve basic skill using this training";
        $training4->user_id = $userlp2->id;
        $training4->is_paid = 0;
        $training4->created_by = $userlp2->id;
        $training4->save();

        $training5 = new LPTraining();
        $training5->name = 'C Programining (Advance)';
        $training5->slug = Str::slug('Python (Advance)');
        $training5->date = date('Y-m-d h:i:s');
        $training5->link =  'Python (Advance)';
        $training5->description = "This C Programining Advance training imporve basic skill using this training";
        $training5->user_id = $userlp2->id;
        $training5->is_paid = 0;
        $training5->created_by = $userlp2->id;
        $training5->save();


        for ($i = 1; $i <= 10; $i++) {
            $userlpu = new User();
            $userlpu->first_name = "Demo" . $i;
            $userlpu->last_name = "LPUser" . $i;
            $userlpu->slug = Str::slug("Demo$i LPUser$i");
            $userlpu->mobile_no = "222223333" . $i;
            $userlpu->password = bcrypt("demolpu@123");
            $userlpu->email = "demolpuser$i@educloudlabs.com";
            $userlpu->role = 5;
            $userlpu->parent_id = $i <= 5 ? $userlp1->id : $userlp2->id;
            $userlpu->save();

            if ($i <= 5) {
                $lptuser = new LPTUser();
                $lptuser->user_id = $userlpu->id;
                $lptuser->training_id = $training1->id;
                $lptuser->provider_id = $userlp1->id;
                $lptuser->save();

                $lptuser = new LPTUser();
                $lptuser->user_id = $userlpu->id;
                $lptuser->training_id = $training2->id;
                $lptuser->provider_id = $userlp1->id;
                $lptuser->save();

                $lptuser = new LPTUser();
                $lptuser->user_id = $userlpu->id;
                $lptuser->training_id = $training3->id;
                $lptuser->provider_id = $userlp1->id;
                $lptuser->save();
            } else {
                $lptuser = new LPTUser();
                $lptuser->user_id = $userlpu->id;
                $lptuser->training_id = $training4->id;
                $lptuser->provider_id = $userlp2->id;
                $lptuser->save();

                $lptuser = new LPTUser();
                $lptuser->user_id = $userlpu->id;
                $lptuser->training_id = $training5->id;
                $lptuser->provider_id = $userlp2->id;
                $lptuser->save();
            }
        }
    }
}
