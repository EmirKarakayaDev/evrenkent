<?php

namespace Tests;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Roller (super_admin, dergi_editoru, yazar, okur) uygulamanın temel bir parçası —
     * migration'lara benzer şekilde her testte otomatik seed edilir (RefreshDatabase kullanan
     * testler için). Kayıt akışı gibi rol atayan her işlem buna bağımlı.
     */
    protected $seed = true;

    protected $seeder = RoleSeeder::class;
}
