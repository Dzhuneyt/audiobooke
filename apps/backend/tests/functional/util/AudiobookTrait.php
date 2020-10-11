<?php


namespace tests\functional\util;


use common\models\Audiobook;
use Faker\Factory;
use Faker\Provider\Address;



trait AudiobookTrait
{

    /**
     * @return Audiobook
     */
    public function createAudiobook()
    {
        $faker = Factory::create();
        $audiobook = new Audiobook();
        $audiobook->title = $faker->text(100);
        $audiobook->copyright_year = $faker->year();
        $audiobook->description = $faker->text(500);
        $audiobook->save(false);
        return $audiobook;
    }

}
