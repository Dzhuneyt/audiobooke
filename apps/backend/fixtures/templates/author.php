<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$yearOfDeath = $faker->year();
$yearOfBirth = $faker->year($yearOfDeath);
return [
    'firstname' => $faker->firstName,
    'lastname' => $faker->lastName,
    'dob' => $yearOfBirth,
    'dod' => $yearOfDeath,
];
