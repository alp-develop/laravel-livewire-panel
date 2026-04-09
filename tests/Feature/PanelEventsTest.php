<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Events\LoginAttempted;
use AlpDevelop\LivewirePanel\Events\PanelAccessDenied;
use AlpDevelop\LivewirePanel\Events\PanelEvent;
use AlpDevelop\LivewirePanel\Events\UserCreated;
use AlpDevelop\LivewirePanel\Events\UserDeleted;
use AlpDevelop\LivewirePanel\Events\UserLoggedIn;
use AlpDevelop\LivewirePanel\Events\UserLoggedOut;
use AlpDevelop\LivewirePanel\Events\UserRegistered;
use AlpDevelop\LivewirePanel\Events\UserUpdated;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PanelEventsTest extends PanelTestCase
{
    public function test_login_attempted_event_stores_data(): void
    {
        $event = new LoginAttempted('admin', 'user@test.com', true, 'web', '127.0.0.1');

        $this->assertEquals('admin', $event->panelId);
        $this->assertEquals('user@test.com', $event->email);
        $this->assertTrue($event->successful);
        $this->assertEquals('web', $event->guard);
        $this->assertEquals('127.0.0.1', $event->ip);
        $this->assertNotNull($event->timestamp);
    }

    public function test_user_logged_in_event_stores_data(): void
    {
        $event = new UserLoggedIn('admin', 1, 'web', '10.0.0.1');

        $this->assertEquals(1, $event->userId);
        $this->assertEquals('web', $event->guard);
    }

    public function test_user_logged_out_event_stores_data(): void
    {
        $event = new UserLoggedOut('admin', 'web', '127.0.0.1');

        $this->assertEquals('web', $event->guard);
    }

    public function test_user_registered_event_stores_data(): void
    {
        $event = new UserRegistered('admin', 5, 'new@test.com', 'web', '10.0.0.1');

        $this->assertEquals(5, $event->userId);
        $this->assertEquals('new@test.com', $event->email);
    }

    public function test_user_created_event_stores_data(): void
    {
        $event = new UserCreated('admin', 10, 'created@test.com', 1, '127.0.0.1');

        $this->assertEquals(10, $event->userId);
        $this->assertEquals('created@test.com', $event->email);
        $this->assertEquals(1, $event->createdBy);
    }

    public function test_user_updated_event_stores_data(): void
    {
        $event = new UserUpdated('admin', 10, 1, '127.0.0.1');

        $this->assertEquals(10, $event->userId);
        $this->assertEquals(1, $event->updatedBy);
    }

    public function test_user_deleted_event_stores_data(): void
    {
        $event = new UserDeleted('admin', 10, 1, '127.0.0.1');

        $this->assertEquals(10, $event->userId);
        $this->assertEquals(1, $event->deletedBy);
    }

    public function test_panel_access_denied_event_stores_data(): void
    {
        $event = new PanelAccessDenied('admin', 5, 'unauthorized', '127.0.0.1');

        $this->assertEquals(5, $event->userId);
        $this->assertEquals('unauthorized', $event->reason);
    }

    public function test_events_have_timestamp(): void
    {
        $event = new LoginAttempted('admin', 'a@b.com', false, 'web');

        $this->assertNotNull($event->timestamp);
        $this->assertIsString($event->timestamp);
    }

    public function test_events_accept_null_ip(): void
    {
        $event = new UserCreated('admin', 1, 'a@b.com', null, null);

        $this->assertNull($event->ip);
        $this->assertNull($event->createdBy);
    }
}
