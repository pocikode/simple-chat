<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    private $name = ['Fitra Aziz', 'Soleh Zuam', 'Muhammad Ilham', 'Adi Aswara', 'Luthfi Aji', 'Wintemas Miko', 'Yusup Almaududi', 'Syarif Hidayatullah'];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < count($this->name); $i++) {
            DB::table('users')->insert([
                'name' => $this->name[$i],
                'phone' => $this->generatePhone(),
                'photo_profile' => "http://api-simple-chat.herokuapp.com/images/default-user-photo.png",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
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
