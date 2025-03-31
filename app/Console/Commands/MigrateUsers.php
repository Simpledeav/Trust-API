<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Wallet;

class MigrateUsers extends Command
{
    protected $signature = 'app:migrate-users';
    protected $description = 'Migrate users from App 1 to App 2';

    public function handle()
    {
        // Configure connection to App 2's database
        config(['database.connections.app2' => [
            'driver' => 'mysql',
            'host' => env('APP2_DB_HOST', '127.0.0.1'),
            'port' => env('APP2_DB_PORT', '3306'),
            'database' => env('APP2_DB_DATABASE', 'forge'),
            'username' => env('APP2_DB_USERNAME', 'forge'),
            'password' => env('APP2_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ]]);
        
        // Get users from App 2
        $app2Users = DB::connection('app2')->table('users')->get();
        
        $this->info("Found {$app2Users->count()} users to migrate");
        
        $bar = $this->output->createProgressBar(count($app2Users));
        
        foreach ($app2Users as $app2User) {
            try {
                // Map App 2 fields to App 1 schema
                $userData = [
                    'id' => Str::uuid(),
                    'first_name' => $app2User->first_name,
                    'last_name' => $app2User->last_name,
                    'username' => $app2User->username,
                    'email' => $app2User->email,
                    'phone' => $app2User->phone,
                    'address' => $app2User->address,
                    'city' => $app2User->city,
                    'zipcode' => $app2User->zip_code ?? '',
                    'ssn' => $app2User->ssn,
                    'dob' => $app2User->dob ?: null,
                    'nationality' => $app2User->nationality,
                    'experience' => $this->mapExperience($app2User->experience),
                    'employed' => $this->mapEmployment($app2User->employment),
                    'status' => $this->mapStatus($app2User->status),
                    'kyc' => $this->mapKycStatus($app2User->status),
                    'email_verified_at' => $app2User->email_verified_at,
                    'password' => $app2User->password, // Keep same hashed password
                    'avatar' => $app2User->photo,
                    'front_id' => $app2User->passport ?? $app2User->drivers_license ?? $app2User->state_id,
                    'back_id' => null, // App 2 doesn't have back ID
                    'blocked_at' => $app2User->status === 'suspended' ? now() : null,
                    // You'll need to set these based on your App 1 requirements
                    'country_id' => $this->getCountryId($app2User->country),
                    'state_id' => $this->getStateId($app2User->state),
                    'currency_id' => $this->getDefaultCurrencyId(),
                    'id_number' => null, // Not in App 2
                ];
                
                // Create user in App 1
                $user = User::create($userData);
                
                // Create wallet for user
                Wallet::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);

                $user->storePayment('admin', []);
                $user->storePayment('user', []);
                
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Failed to migrate user {$app2User->id}: " . $e->getMessage());
                continue;
            }
        }
        
        $bar->finish();
        $this->info("\nUser migration completed!");
    }
    
    // Helper methods to map values between apps
    protected function mapExperience($exp)
    {
        $map = [
            'none' => '0 years',
            'beginner' => '1 year',
            'amateur' => '2 years',
            'expert' => '10+ years',
        ];
        return $map[$exp] ?? '1 year';
    }
    
    protected function mapEmployment($employment)
    {
        $map = [
            'employed' => 'Yes',
            'unemployed' => 'No',
            'retired' => 'No',
            'student' => 'No',
        ];
        return $map[$employment] ?? 'No';
    }
    
    protected function mapStatus($status)
    {
        $map = [
            'pending' => 'active',
            'approved' => 'active',
            'declined' => 'active', // or 'suspended' depending on your needs
            'suspended' => 'suspended',
        ];
        return $map[$status] ?? 'active';
    }
    
    protected function mapKycStatus($status)
    {
        $map = [
            'pending' => 'pending',
            'approved' => 'approved',
            'declined' => 'declined',
            'suspended' => 'pending',
        ];
        return $map[$status] ?? 'pending';
    }
    
    protected function getCountryId($countryName)
    {
        // Implement logic to find country ID by name in App 1
        return \App\Models\Country::where('name', $countryName)->first()->id ?? 
               \App\Models\Country::first()->id;
    }
    
    protected function getStateId($stateName)
    {
        // Implement logic to find state ID by name in App 1
        return \App\Models\State::where('name', $stateName)->first()->id ?? 
               \App\Models\State::first()->id;
    }
    
    protected function getDefaultCurrencyId()
    {
        return \App\Models\Currency::first()->id;
    }
}