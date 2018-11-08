<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        DB::table('users')->insert([
            'name'  => $faker->name,
            'phone' => $this->generatePhone(),
            'photo_profile' => "http://localhost:8000/images/default-user-photo.png",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]) ;
    }

    private function generatePhone()
    {
        $phone = $this->getProvider() . $this->getNumber();
        return $phone;
    }

    private function getProvider()
    {
        $prov = ['0813','0812','0877','0852','0815','0896','0857','0895'];
        $getProv = $prov[rand(0,count($prov)-1)];
        return $getProv;
    }

    private function getNumber()
    {
        $no = ['0','1','2','3','4','5','6','7','8','9'];

        $res = '';
        for ($i=0; $i < 8; $i++) { 
            $res .= $no[rand(0,9)];
        }

        return $res;
    }
}
