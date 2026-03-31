<?php

namespace Tests\Unit;

use App\Helpers\PostcodeHelper;
use App\Rules\UkPostcode;
use PHPUnit\Framework\TestCase;

class PostcodeHelperTest extends TestCase
{
    // --- PostcodeHelper::format ---

    public function test_format_lowercase_no_space(): void
    {
        $this->assertEquals('SW1A 1AA', PostcodeHelper::format('sw1a1aa'));
    }

    public function test_format_already_correct(): void
    {
        $this->assertEquals('SW1A 1AA', PostcodeHelper::format('SW1A 1AA'));
    }

    public function test_format_with_extra_spaces(): void
    {
        $this->assertEquals('SW1A 1AA', PostcodeHelper::format('  SW1A  1AA  '));
    }

    public function test_format_short_postcode(): void
    {
        $this->assertEquals('E1 6AN', PostcodeHelper::format('e16an'));
    }

    public function test_format_medium_postcode(): void
    {
        $this->assertEquals('EC1A 1BB', PostcodeHelper::format('ec1a1bb'));
    }

    public function test_format_preserves_gir(): void
    {
        $this->assertEquals('GIR 0AA', PostcodeHelper::format('gir0aa'));
    }

    // --- PostcodeHelper::outwardCode ---

    public function test_outward_code_standard(): void
    {
        $this->assertEquals('SW1A', PostcodeHelper::outwardCode('SW1A 1AA'));
    }

    public function test_outward_code_short(): void
    {
        $this->assertEquals('E1', PostcodeHelper::outwardCode('E1 6AN'));
    }

    public function test_outward_code_from_unformatted(): void
    {
        $this->assertEquals('SW1A', PostcodeHelper::outwardCode('sw1a1aa'));
    }

    public function test_outward_code_four_char(): void
    {
        $this->assertEquals('EC1A', PostcodeHelper::outwardCode('EC1A 1BB'));
    }

    // --- PostcodeHelper::area ---

    public function test_area_two_letter(): void
    {
        $this->assertEquals('SW', PostcodeHelper::area('SW1A 1AA'));
    }

    public function test_area_single_letter(): void
    {
        $this->assertEquals('E', PostcodeHelper::area('E1 6AN'));
    }

    public function test_area_two_letter_ec(): void
    {
        $this->assertEquals('EC', PostcodeHelper::area('EC1A 1BB'));
    }

    public function test_area_lowercase(): void
    {
        $this->assertEquals('SW', PostcodeHelper::area('sw1a 1aa'));
    }

    public function test_area_manchester(): void
    {
        $this->assertEquals('M', PostcodeHelper::area('M1 1AA'));
    }

    public function test_area_birmingham(): void
    {
        $this->assertEquals('B', PostcodeHelper::area('B1 1BB'));
    }

    // --- UkPostcode validation rule ---

    public function test_valid_postcodes_pass_validation(): void
    {
        $rule = new UkPostcode();
        $validPostcodes = [
            'SW1A 1AA',
            'EC1A 1BB',
            'W1A 0AX',
            'M1 1AE',
            'B33 8TH',
            'CR2 6XH',
            'DN55 1PT',
            'GIR 0AA',
            'E1 6AN',
            'LS1 4AP',
        ];

        foreach ($validPostcodes as $postcode) {
            $failed = false;
            $rule->validate('postcode', $postcode, function () use (&$failed) {
                $failed = true;
            });
            $this->assertFalse($failed, "Expected '{$postcode}' to be valid but it failed.");
        }
    }

    public function test_invalid_postcodes_fail_validation(): void
    {
        $rule = new UkPostcode();
        $invalidPostcodes = [
            '12345',
            'INVALID',
            '',
            'AAA 1AA',
            'SW1A',
            '123 456',
        ];

        foreach ($invalidPostcodes as $postcode) {
            $failed = false;
            $rule->validate('postcode', $postcode, function () use (&$failed) {
                $failed = true;
            });
            $this->assertTrue($failed, "Expected '{$postcode}' to be invalid but it passed.");
        }
    }

    public function test_valid_postcode_without_space(): void
    {
        $rule = new UkPostcode();
        $failed = false;
        $rule->validate('postcode', 'SW1A1AA', function () use (&$failed) {
            $failed = true;
        });
        $this->assertFalse($failed, "Expected 'SW1A1AA' (no space) to be valid.");
    }

    public function test_valid_postcode_lowercase(): void
    {
        $rule = new UkPostcode();
        $failed = false;
        $rule->validate('postcode', 'sw1a 1aa', function () use (&$failed) {
            $failed = true;
        });
        $this->assertFalse($failed, "Expected lowercase 'sw1a 1aa' to be valid.");
    }
}
