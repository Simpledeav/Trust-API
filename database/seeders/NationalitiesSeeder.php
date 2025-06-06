<?php

namespace Database\Seeders;

use App\Models\Nationalities;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NationalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $nationalities = [
        //     "Afghan", "Albanian", "Algerian", "American", "Andorran", "Angolan", "Anguillan", "Argentine", "Armenian", "Australian", "Austrian", "Azerbaijani",
        //     "Bahamian", "Bahraini", "Bangladeshi", "Barbadian", "Belarusian", "Belgian", "Belizean", "Beninese", "Bermudian", "Bhutanese", "Bolivian", "Botswanan",
        //     "Brazilian", "British", "British Virgin Islander", "Bruneian", "Bulgarian", "Burkinan", "Burmese", "Burundian", "Cambodian", "Cameroonian", "Canadian",
        //     "Cape Verdean", "Cayman Islander", "Central African", "Chadian", "Chilean", "Chinese", "Citizen of Antigua and Barbuda", "Citizen of Bosnia and Herzegovina",
        //     "Citizen of Guinea-Bissau", "Citizen of Kiribati", "Citizen of Seychelles", "Citizen of the Dominican Republic", "Citizen of Vanuatu", "Colombian", "Comoran",
        //     "Congolese (Congo)", "Congolese (DRC)", "Cook Islander", "Costa Rican", "Croatian", "Cuban", "Cymraes", "Cymro", "Cypriot", "Czech", "Danish", "Djiboutian",
        //     "Dominican", "Dutch", "East Timorese", "Ecuadorean", "Egyptian", "Emirati", "English", "Equatorial Guinean", "Eritrean", "Estonian", "Ethiopian", "Faroese",
        //     "Fijian", "Filipino", "Finnish", "French", "Gabonese", "Gambian", "Georgian", "German", "Ghanaian", "Gibraltarian", "Greek", "Greenlandic", "Grenadian",
        //     "Guamanian", "Guatemalan", "Guinean", "Guyanese", "Haitian", "Honduran", "Hong Konger", "Hungarian", "Icelandic", "Indian", "Indonesian", "Iranian", "Iraqi",
        //     "Irish", "Israeli", "Italian", "Ivorian", "Jamaican", "Japanese", "Jordanian", "Kazakh", "Kenyan", "Kittitian", "Kosovan", "Kuwaiti", "Kyrgyz", "Lao",
        //     "Latvian", "Lebanese", "Liberian", "Libyan", "Liechtenstein citizen", "Lithuanian", "Luxembourger", "Macanese", "Macedonian", "Malagasy", "Malawian",
        //     "Malaysian", "Maldivian", "Malian", "Maltese", "Marshallese", "Martiniquais", "Mauritanian", "Mauritian", "Mexican", "Micronesian", "Moldovan", "Monegasque",
        //     "Mongolian", "Montenegrin", "Montserratian", "Moroccan", "Mosotho", "Mozambican", "Namibian", "Nauruan", "Nepalese", "New Zealander", "Nicaraguan",
        //     "Nigerian", "Nigerien", "Niuean", "North Korean", "Northern Irish", "Norwegian", "Omani", "Pakistani", "Palauan", "Palestinian", "Panamanian",
        //     "Papua New Guinean", "Paraguayan", "Peruvian", "Pitcairn Islander", "Polish", "Portuguese", "Prydeinig", "Puerto Rican", "Qatari", "Romanian", "Russian",
        //     "Rwandan", "Salvadorean", "Sammarinese", "Samoan", "Sao Tomean", "Saudi Arabian", "Scottish", "Senegalese", "Serbian", "Sierra Leonean", "Singaporean",
        //     "Slovak", "Slovenian", "Solomon Islander", "Somali", "South African", "South Korean", "South Sudanese", "Spanish", "Sri Lankan", "St Helenian", "St Lucian",
        //     "Stateless", "Sudanese", "Surinamese", "Swazi", "Swedish", "Swiss", "Syrian", "Taiwanese", "Tajik", "Tanzanian", "Thai", "Togolese", "Tongan", "Trinidadian",
        //     "Tristanian", "Tunisian", "Turkish", "Turkmen", "Turks and Caicos Islander", "Tuvaluan", "Ugandan", "Ukrainian", "Uruguayan", "Uzbek", "Vatican citizen",
        //     "Venezuelan", "Vietnamese", "Vincentian", "Wallisian", "Welsh", "Yemeni", "Zambian", "Zimbabwean"
        // ];        
        
        // $data = collect($nationalities)->map(fn($name) => [
        //     'id' => Str::uuid(),
        //     'name' => $name,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('nationalities')->insert($data->toArray());


        try {
            // Fetch all users where nationality is a UUID that exists in the nationalities table
            $affectedUsers = User::all()->filter(function ($user) {
                return Nationalities::where('id', $user->nationality)->exists();
            });

            foreach ($affectedUsers as $user) {
                $nationality = Nationalities::find($user->nationality);
                if ($nationality) {
                    $user->update(['nationality' => $nationality->name]);
                }
            }

            DB::commit();
            $this->command->info("All affected users' nationality fields were updated successfully.\n");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->info("Error updating users: " . $e->getMessage());
        }
    }
}
