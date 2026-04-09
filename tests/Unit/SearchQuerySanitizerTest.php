<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Security\SearchQuerySanitizer;
use PHPUnit\Framework\TestCase;

final class SearchQuerySanitizerTest extends TestCase
{
    public function test_escapes_percent_wildcard(): void
    {
        $this->assertSame('test\%query', SearchQuerySanitizer::sanitize('test%query'));
    }

    public function test_escapes_underscore_wildcard(): void
    {
        $this->assertSame('test\_query', SearchQuerySanitizer::sanitize('test_query'));
    }

    public function test_truncates_to_max_length(): void
    {
        $long = str_repeat('a', 200);

        $this->assertSame(100, mb_strlen(SearchQuerySanitizer::sanitize($long)));
    }

    public function test_custom_max_length(): void
    {
        $input = str_repeat('b', 50);

        $this->assertSame(20, mb_strlen(SearchQuerySanitizer::sanitize($input, 20)));
    }

    public function test_preserves_normal_input(): void
    {
        $this->assertSame('john doe', SearchQuerySanitizer::sanitize('john doe'));
    }

    public function test_handles_empty_string(): void
    {
        $this->assertSame('', SearchQuerySanitizer::sanitize(''));
    }

    public function test_escapes_multiple_wildcards(): void
    {
        $this->assertSame(
            '\%admin\_test\%',
            SearchQuerySanitizer::sanitize('%admin_test%')
        );
    }

    public function test_handles_unicode(): void
    {
        $this->assertSame('usuario', SearchQuerySanitizer::sanitize('usuario'));
    }
}
