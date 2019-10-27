<?php

namespace Tests\Feature;

use Tests\TestCase;

class InitCommandTest extends TestCase
{
    public function tearDown() : void
    {
        unlink('./tests/fixtures/.s3backup/config.json');
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInitCommand()
    {
        $configPath = "./tests/fixtures/.s3backup";

        $this->artisan('init')
             ->expectsOutput('The configuration file has been created.')
             ->assertExitCode(0);

        $this->assertDirectoryExists($configPath);
        $this->assertFileExists($configPath . "/config.json");
    }
}
