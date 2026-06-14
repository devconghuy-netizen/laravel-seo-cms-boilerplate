<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledViewsPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'affipress-testing-views';

        File::ensureDirectoryExists($compiledViewsPath);
        config(['view.compiled' => $compiledViewsPath]);
    }
}
