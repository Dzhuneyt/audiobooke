<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

return [
    'title' => ucwords($faker->words(3, true)),
    'description' => $faker->text(500),
    'language' => 'English',
    'copyright_year' => $faker->numberBetween(1600, 2019),
    'num_sections' => $faker->numberBetween(1, 99),
    'url_zip_file' => $faker->url,
    'totaltimesecs' => $faker->numberBetween(120, 999999999),
];
