<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['username' => 'andres', 'email' => 'andres@gmail.com', 'password' => Hash::make("12345678"),'type' => "admin" ,'created_at' => "2023-01-30 11:00:00"],
            ['username' => 'ismael', 'email' => 'ismael@gmail.com', 'password' => Hash::make("123456"),'type' => "profesional" ,'created_at' => "2023-01-30 11:00:00"],
            ['username' => 'pablo', 'email' => 'pablo@gmail.com', 'password' => Hash::make("21342efd"),'type' => "particular" ,'created_at' => "2023-01-30 11:00:00"],
            ['username' => 'carlos', 'email' => 'carlos@gmail.com', 'password' => Hash::make("453213"),'type' => "profesional" ,'created_at' => "2023-01-30 11:00:00"],
            ['username' => 'lucia', 'email' => 'lucia@gmail.com', 'password' => Hash::make("andres21"),'type' => "admin" ,'created_at' => "2023-01-30 11:00:00"]
        ]);

        DB::table('collections')->insert([
            ['name' => 'Overwatch', 'symbol' => 'https://m.media-amazon.com/images/I/51OahAdeNYL._SX323_BO1,204,203,200_.jpg', 'editDate' => "2023-01-30 11:00:00", 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Metal Bands', 'symbol' => 'https://m.media-amazon.com/images/I/71rUot6zZ1L.png','editDate' => "2023-01-30 11:00:00", 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Films', 'symbol' => 'https://www.newstatesman.com/wp-content/uploads/sites/2/2021/12/2ATHYW0-1038x778.jpg','editDate' => "2023-01-30 11:00:00", 'created_at' => "2023-01-30 11:00:00"],
        ]);

        DB::table('cards')->insert([
            ['name' => 'Tracer', 'description' => 'Pesada', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Roadhog', 'description' => 'Tremendo nerf', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Kiriko', 'description' => 'Mejor Support', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Lorna Shore', 'description' => 'Dark Metal', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Powerwolf', 'description' => 'Power Metal', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Dream Theater', 'description' => 'Alternative Metal', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Buscando a Nemo', 'description' => 'Película infantil', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'Gladiator', 'description' => 'Película sangrienta y cruel', 'created_at' => "2023-01-30 11:00:00"],
            ['name' => 'EL único superviviente', 'description' => 'Película de guerra sangrienta', 'created_at' => "2023-01-30 11:00:00"]
        ]);

        DB::table('card_collection')->insert([
            ['card_id' => 1, 'collection_id' => 1,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 2, 'collection_id' => 1,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 3, 'collection_id' => 1,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 4, 'collection_id' => 2,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 5, 'collection_id' => 2,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 6, 'collection_id' => 2,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 7, 'collection_id' => 3,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 8, 'collection_id' => 3,'created_at' => "2023-01-30 11:00:00"],
            ['card_id' => 9, 'collection_id' => 3,'created_at' => "2023-01-30 11:00:00"],
        ]);

        DB::table('adverts')->insert([
            ['user_id' => 1, 'card_id' => 1, 'nºcards' => 5, 'price' => 100, 'created_at' => "2022-12-12 23:00:00"],
            ['user_id' => 1, 'card_id' => 2, 'nºcards' => 2, 'price' => 25, 'created_at' => "2022-12-12 23:00:00"],
            ['user_id' => 2, 'card_id' => 3, 'nºcards' => 8, 'price' => 65, 'created_at' => "2022-12-12 23:00:00"],
            ['user_id' => 3, 'card_id' => 5, 'nºcards' => 4, 'price' => 75, 'created_at' => "2022-12-12 23:00:00"],
            ['user_id' => 4, 'card_id' => 8, 'nºcards' => 1, 'price' => 250, 'created_at' => "2022-12-12 23:00:00"],
            ['user_id' => 5, 'card_id' => 7, 'nºcards' => 15, 'price' => 20, 'created_at' => "2022-12-12 23:00:00"],
        ]);
    }
}
