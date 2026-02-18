<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            Artisan::call('route:clear');
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
            // ignore in case commands are unavailable
        }
    }
}
