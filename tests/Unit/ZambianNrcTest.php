<?php

namespace Tests\Unit;

use App\Rules\ZambianNrc as ZambianNrcRule;
use App\Support\ZambianNrc;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ZambianNrcTest extends TestCase
{
    public function test_accepts_canonical_nrc_format(): void
    {
        $this->assertTrue(ZambianNrc::isValid('123456/78/1'));
        $this->assertSame('123456/78/1', ZambianNrc::normalize('123456781'));
        $this->assertSame('123456/78/1', ZambianNrc::normalize('123456/78/1'));
    }

    public function test_rejects_invalid_lengths_and_characters(): void
    {
        $this->assertFalse(ZambianNrc::isValid('123456/78'));
        $this->assertFalse(ZambianNrc::isValid('123456/78/12'));
        $this->assertFalse(ZambianNrc::isValid('123456/78/901'));
        $this->assertFalse(ZambianNrc::isValid('ABCDEF/78/1'));
        $this->assertNull(ZambianNrc::normalize('12345678'));
    }

    public function test_validation_rule_rejects_bad_nrc(): void
    {
        $validator = Validator::make(
            ['nrc' => '123456/78'],
            ['nrc' => ['required', 'string', new ZambianNrcRule]]
        );

        $this->assertTrue($validator->fails());
    }
}
