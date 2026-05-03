<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use PHPUnit\Framework\TestCase;

final class PanelAccessRegistryTest extends TestCase
{
    private PanelAccessRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new PanelAccessRegistry();
    }

    public function test_has_returns_false_when_not_registered(): void
    {
        $this->assertFalse($this->registry->has('admin'));
    }

    public function test_has_returns_true_after_registration(): void
    {
        $this->registry->for('admin', fn ($u) => true);
        $this->assertTrue($this->registry->has('admin'));
    }

    public function test_check_returns_callable_result(): void
    {
        $this->registry->for('admin', fn ($u) => $u === 'valid-user');

        $this->assertTrue($this->registry->check('admin', 'valid-user'));
        $this->assertFalse($this->registry->check('admin', 'other-user'));
    }

    public function test_find_panel_returns_null_when_empty(): void
    {
        $this->assertNull($this->registry->findPanel('user'));
    }

    public function test_find_panel_returns_null_when_no_match(): void
    {
        $this->registry->for('admin', fn ($u) => false);
        $this->assertNull($this->registry->findPanel('user'));
    }

    public function test_find_panel_returns_matching_panel(): void
    {
        $this->registry->for('editor', fn ($u) => false);
        $this->registry->for('admin', fn ($u) => $u === 'superuser');

        $result = $this->registry->findPanel('superuser');
        $this->assertSame('admin', $result);
    }

    public function test_find_panel_returns_first_matching_panel(): void
    {
        $this->registry->for('panel-a', fn ($u) => true);
        $this->registry->for('panel-b', fn ($u) => true);

        $result = $this->registry->findPanel('any');
        $this->assertSame('panel-a', $result);
    }

    public function test_overwriting_check_for_same_panel(): void
    {
        $this->registry->for('admin', fn ($u) => false);
        $this->registry->for('admin', fn ($u) => true);

        $this->assertTrue($this->registry->check('admin', 'anyone'));
    }
}
