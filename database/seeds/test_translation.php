<?php

use Illuminate\Database\Seeder;

class test_translation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fragment = new App\Fragment();
        $fragment->key = 'home.greeting';
        $fragment->setTranslation('text', 'en', 'Hello world!');
        $fragment->save();
    }
}
