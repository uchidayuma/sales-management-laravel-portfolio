<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\SameCustomerContactsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(AreaOpenEmailSendsTableSeeder::class);
        $this->call(ArticlesTableSeeder::class);
        $this->call(RanksTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ConfigsTableSeeder::class);
        $this->call(ContactTypesTableSeeder::class);
        $this->call(ContactsTableSeeder::class);
        $this->call(OldProductsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(QuotationsTableSeeder::class);
        $this->call(ProductQuotationMaterialsTableSeeder::class);
        $this->call(ProductQuotationsTableSeeder::class);
        $this->call(ProductTypesTableSeeder::class);
        $this->call(PrefecturesTableSeeder::class);
        $this->call(StepsTableSeeder::class);
        $this->call(FcApplyAreasTableSeeder::class);
        $this->call(NotificationTypesTableSeeder::class);
        $this->call(UserNotificationsTableSeeder::class);
        $this->call(ShippingsTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(ProductTransactionsTableSeeder::class);
        $this->call(OfficeHolidaysTableSeeder::class);
        $this->call(SameCustomerContactsTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
