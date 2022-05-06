<?php

namespace HackerESQ\Settings\Tests;


use Illuminate\Filesystem\Cache;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\DB;
use HackerESQ\Settings\Facades\Settings;

class SettingsTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // environment specific
        $app['config']->set('app.debug', env('APP_DEBUG', true));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        // package specific
        $app['config']->set('settings.encrypt', []);
        $app['config']->set('settings.fillable', []);
        $app['config']->set('settings.cache', true);
        $app['config']->set('settings.hidden', []);
        $app['config']->set('settings.cast', []);
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(__DIR__ . '/../database/migrations')
        ]);
    }

    /** 
     * Get package providers.
     * 
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [\HackerESQ\Settings\SettingsServiceProvider::class];
    }

    /**
     * Get package aliases.
     * 
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Settings' => 'HackerESQ\Settings\Facades\Settings',
        ];
    }

    /** @test */
    public function it_can_set_and_get_settings()
    {
        $this->app['config']->set('settings.fillable', ['key']);

        $result = Settings::set(['key' => 'value']);

        $this->assertTrue($result);

        $result = Settings::get('key');

        $this->assertEquals($result, 'value');
    }

    /** @test */
    public function it_can_force_set_settings()
    {
        $result = Settings::force()->set(['forced' => 'value']);

        $this->assertTrue($result);

        $result = Settings::get('forced');

        $this->assertEquals($result, 'value');
    }

    /** @test */
    public function it_can_get_one_setting()
    {
        $this->app['config']->set('settings.fillable', ['foo']);

        Settings::set(['foo' => 'value']);

        $result = Settings::get('foo');

        $this->assertEquals('value', $result, json_encode($result));
    }

    /** @test */
    public function it_can_get_many_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);

        Settings::set(['foo' => 'value']);
        Settings::set(['bar' => 'value']);

        $result = Settings::get();

        $this->assertArrayHasKey('foo', $result, json_encode($result));
        $this->assertArrayHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_get_array_of_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);

        Settings::set(['foo' => 'value']);
        Settings::set(['bar' => 'value']);

        $result = Settings::get(['foo', 'bar']);

        $this->assertArrayHasKey('foo', $result, json_encode($result));
        $this->assertArrayHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_set_many_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        $result = Settings::get();

        $this->assertArrayHasKey('foo', $result, json_encode($result));
        $this->assertArrayHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_return_has_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value',
        ]);

        $result = Settings::has('foo');

        $this->assertTrue($result, json_encode($result));

        $result = Settings::has(['foo', 'bar']);

        $this->assertTrue($result, json_encode($result));

        $result = Settings::has('not_here');

        $this->assertFalse($result, json_encode($result));
    }

    /** @test */
    public function it_can_encrypt_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);
        $this->app['config']->set('settings.encrypt', ['foo']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        // should be encrypted
        $result = DB::table('settings')->select('value')->where('key', 'foo')->first();
        $this->assertNotEquals('value', $result->value, json_encode($result));

        // should NOT be encrypted
        $result = DB::table('settings')->select('value')->where('key', 'bar')->first();
        $this->assertEquals('value', $result->value, json_encode($result));
    }

    /** @test */
    public function it_can_hide_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);
        $this->app['config']->set('settings.hidden', ['foo']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        $result = Settings::get();
        $this->assertArrayNotHasKey('foo', $result, json_encode($result));
        $this->assertArrayHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_hide_all_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);
        $this->app['config']->set('settings.hidden', ['*']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        $result = Settings::get();
        $this->assertArrayNotHasKey('foo', $result, json_encode($result));
        $this->assertArrayNotHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_prevent_fill_settings()
    {
        $this->app['config']->set('settings.fillable', []);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        $result = Settings::get();

        $this->assertArrayNotHasKey('foo', $result, json_encode($result));
        $this->assertArrayNotHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_fill_settings()
    {
        $this->app['config']->set('settings.fillable', ['*']);

        Settings::set([
            'foo' => 'value',
            'bar' => 'value'
        ]);

        $result = Settings::get();

        $this->assertArrayHasKey('foo', $result, json_encode($result));
        $this->assertArrayHasKey('bar', $result, json_encode($result));
    }

    /** @test */
    public function it_can_set_and_get_tenant_settings()
    {
        $this->app['config']->set('settings.fillable', ['*']);

        Settings::tenant('test')->set(['foo' => 'bar']);

        $tenant = Settings::tenant('test')->get();

        $this->assertArrayHasKey('foo', $tenant, json_encode($tenant));

        $no_tenant = Settings::tenant()->get();

        $this->assertArrayNotHasKey('foo', $no_tenant, json_encode($no_tenant));
    }

    /** @test */
    public function it_can_return_default_string_settings()
    {
        $settings = Settings::get('baz', 'test');

        $this->assertEquals('test', $settings, json_encode($settings));
    }

    /** @test */
    public function it_can_return_default_array_settings()
    {
        $this->app['config']->set('settings.fillable', ['foo', 'bar']);

        Settings::set([
            'foo' => 'bar',
            'bar' => 'baz'
        ]);

        $settings = Settings::get(['foo', 'bar', 'not_set'], ['not_set' => 'test']);

        $this->assertEquals('test', $settings['not_set'], json_encode($settings));
    }

    /** @test */
    public function it_can_return_default_all_settings()
    {
        $settings = Settings::get(['not_set'], ['not_set' => 'test']);

        $this->assertEquals('test', $settings['not_set'], json_encode($settings));
    }

    /** @test */
    public function it_casts_arrays()
    {
        $this->app['config']->set('settings.fillable', ['*']);
        $this->app['config']->set('settings.cast', ['array' => 'json']);

        Settings::set(['array' => ['test', 'one', 'two']]);

        $settings = Settings::get('array');

        $this->assertIsArray($settings);
    }

    /** @test */
    public function it_casts_booleans()
    {
        $this->app['config']->set('settings.fillable', ['*']);
        $this->app['config']->set('settings.cast', ['boolean' => 'boolean']);

        Settings::set(['boolean' => true]);

        $settings = Settings::get('boolean');

        $this->assertEquals($settings, true);
    }
}
