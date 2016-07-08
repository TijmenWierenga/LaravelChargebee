<?php
namespace TijmenWierenga\LaravelChargebee\Commands;


use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use TijmenWierenga\LaravelChargebee\ChargebeeServiceProvider;

class Install extends Command
{

    protected $signature = 'chargebee:install';

    protected $description = 'Install the full Laravel Chargebee package';

    /**
     * Artisan command prompt
     *
     * @var Kernel
     */
    private $console;

    /**
     * Install constructor.
     */
    public function __construct(Kernel $console)
    {
        parent::__construct();
        $this->console = $console;
    }

    public function handle()
    {
        $this->info('Installing the Chargebee package');
        $bar = $this->output->createProgressBar(2);

        try
        {
            $this->console->call('vendor:publish', [
                '--provider' => ChargebeeServiceProvider::class
            ]);
            $bar->advance();
            $this->console->call('migrate');
            $bar->advance();
        }
        catch (\Exception $e)
        {
            $this->error('Could not fully install the Chargebee package');
        }

        $bar->finish();

        $this->info(PHP_EOL . 'The Chargebee package was successfully installed');
    }
}