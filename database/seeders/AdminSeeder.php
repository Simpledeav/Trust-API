<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Admin;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        activity()->disableLogging();

        DB::beginTransaction();

        $faker = Faker::create();

        // try {
        //     $admin = Admin::query()->updateOrCreate(
        //         [
        //             'email' => 'admin@itrustinvestment.com',
        //         ],
        //         [
        //             'country_id' => Country::query()->where('name', 'nigeria')->value('id'),
        //             'firstname' => 'Admin',
        //             'lastname' => config('app.name'),
        //             'password' => bcrypt('Password@2025'),
        //         ]
        //     );

        //     $role = Role::query()->updateOrCreate(
        //         [
        //             'name' => 'SUPERADMIN',
        //             'description' => 'Superpowered admin',
        //             'guard_name' => 'api_admin',
        //         ],
        //         [
        //             'name' => 'SUPERADMIN',
        //             'description' => 'Superpowered admin',
        //             'guard_name' => 'api_admin',
        //         ]
        //     );

        //     $admin->assignRole($role);

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     throw $e;
        // }

        $firstName = $faker->firstName;

        $admin = Admin::create([
            'country_id' => Country::query()->where('name', 'nigeria')->value('id'),
            'firstname' => $firstName,
            'email' => 'quivorstore@gmail.com',
            'lastname' => config('app.name'),
            'password' => bcrypt('Password@2025'),
        ]);

        $this->command->info('Successfully created ' . $admin->email . ' admin with fake data!');
    }
}
