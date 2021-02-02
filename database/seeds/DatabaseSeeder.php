<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        // // USUARIOS - DAR DE ALTA
        // // 1er USUARIO --- CONTADOR
        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'administrator',
        //         'name'      => 'Genaro Perez', 
        //         'user_name' => 'genaro28',
        //         'email'     => 'genaro@gmail.com',
        //         'password'  => bcrypt('436276G')
        //     ],
        //     1 => [
        //         'role'      => 'administrator',
        //         'name'      => 'Jorge', 
        //         'user_name' => 'jorge25',
        //         'email'     => 'jorge@gmail.com',
        //         'password'  => bcrypt('J567433')
        //     ],
        //     2 => [
        //         'role'      => 'manager',
        //         'name'      => 'Yoselyn', 
        //         'user_name' => 'yoselyn25',
        //         'email'     => 'yoselyn@gmail.com',
        //         'password'  => bcrypt('Y9673N7')
        //     ],
        //     3 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Usuario', 
        //         'user_name' => 'usuario',
        //         'email'     => 'usuario@gmail.com',
        //         'password'  => bcrypt('usuario')
        //     ]
        // ]);

        // // ESCUELAS - DAR DE ALTA
        // \DB::table('schools')->insert([
        //     0 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CAMPECHE' ],
        //     1 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CANCUN' ],
        //     2 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CIUDAD JUAREZ' ],
        //     3 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CUAUTLA' ],
        //     4 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CULIACAN' ],
        //     5 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ESTUDIOS SUP. DE COACALCO' ],
        //     6 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ESTUDIOS SUP. DE LOS CABOS' ],
        //     7 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE HUIMANGUILLO' ],
        //     8 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE IZTAPALAPA' ],
        //     9 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE LINARES' ],
        //     10 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MERIDA' ],
        //     11 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MILPA ALTA' ],
        //     12 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MINATITLAN' ],
        //     13 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE OAXACA' ],
        //     14 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE REYNOSA' ],
        //     15 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ROQUE' ],
        //     16 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE VILLAHERMOSA' ],
        //     17 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ZITACUARO' ],
        //     18 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ACAYUCAN' ],
        //     19 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE APATZINGAN' ],
        //     20 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE CENTLA' ],
        //     21 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE COALCOMAN' ],
        //     22 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ESCARCEGA' ],
        //     23 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE LA COSTA CHICA' ],
        //     24 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE SALVATIERRA' ],
        //     25 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE SAN ANDRES TUXTLA' ],
        //     26 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ZACAPOAXTLA' ],
        //     27 => [ 'name'      => 'TECNOLOGICO DE ESTUDIOS SUPERIORES DE CUAUTITLAN IZCALLI' ],
        //     28 => [ 'name'      => 'VOCACIONAL 7' ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Ana Iturbe', 
        //         'user_name' => 'ana-102',
        //         'email'     => 'ana-iturbe@gmail.com',
        //         'password'  => bcrypt('4n4262')
        //     ],
        //     1=> [
        //         'role'      => 'reviewer',
        //         'name'      => 'Cecilia', 
        //         'user_name' => 'cecilia-11',
        //         'email'     => 'cecilia@gmail.com',
        //         'password'  => bcrypt('c3c1112')
        //     ]
        // ]);

        // \DB::table('editoriales')->insert([
        //     0 => [ 'name'      => 'CAMBRIDGE' ],
        //     1 => [ 'name'      => 'CENGAGE' ],
        //     2 => [ 'name'      => 'EXPRESS PUBLISHING' ],
        //     3 => [ 'name'      => 'MAJESTIC EDUCATION' ],
        //     4 => [ 'name'      => 'MCGRAW HILL' ],
        //     5 => [ 'name'      => 'RICHMOND' ],
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Berenice', 
        //         'user_name' => 'bere-815',
        //         'email'     => 'berenice@gmail.com',
        //         'password'  => bcrypt('b3r368')
        //     ]
        // ]);

        \DB::table('users')->insert([
            // 0 => [
            //     'role'      => 'capturist',
            //     'name'      => 'Armando', 
            //     'user_name' => 'armando-6',
            //     'email'     => 'armando@gmail.com',
            //     'password'  => bcrypt('armando-42')
            // ]
            0 => [
                'role'      => 'capturist',
                'name'      => 'Axel', 
                'user_name' => 'axel-8',
                'email'     => 'axel@gmail.com',
                'password'  => bcrypt('axel-32')
            ],
            1 => [
                'role'      => 'capturist',
                'name'      => 'Angel', 
                'user_name' => 'angel-4',
                'email'     => 'angel@gmail.com',
                'password'  => bcrypt('angel-20')
            ]
        ]);

    }
}
