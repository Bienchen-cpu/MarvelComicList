<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MarvelApiController;


class FetchMarvelData extends Command
{
    protected $signature = 'marvel:fetch';

    protected $description = 'Fetches data from Marvel API and saves it to the database.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $controller = new MarvelApiController();
        $controller->getDataFromMarvelApi();
        $this->info('Data fetched from Marvel API and saved to the database.');
    }
}
