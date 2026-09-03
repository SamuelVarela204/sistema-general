<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_user_role_mapping_supports_cajero_and_trabajador(): void
    {
        $cajero = new User(['id_rol' => 5]);
        $this->assertTrue($cajero->hasRole('cajero'));

        $trabajador = new User(['id_rol' => 6]);
        $this->assertTrue($trabajador->hasRole('trabajador'));
    }
}
