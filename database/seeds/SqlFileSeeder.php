<?php

use Illuminate\Database\Seeder;

class SqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
