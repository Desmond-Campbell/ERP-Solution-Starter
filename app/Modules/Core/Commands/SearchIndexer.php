<?php

namespace App\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\ArgvInput;

use \App\Modules\Core\Models\SearchRegister;
use \App\Modules\Core\Models\SearchIndex;

class SearchIndexer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexer
                                {--mode= : Either create, reset or rebuild}
                                {--limit= : Number of entries to process for create mode}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the search index utility. Create will find modified entities and index them. Reset clears all registered entities for re-indexing. Rebuild does a reset and then re-indexes everything.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $argv = new ArgvInput();
        $mode   = $argv->getParameterOption('--mode', 'create' );
        $limit   = $argv->getParameterOption('--limit', 100 );

        if ( $mode == 'rebuild' || $mode == 'reset' ) {

            SearchRegister::where('index_version', '>', 0)->update( ['index_version' => 0] );

        }

        if ( $mode == 'create' || $mode == 'rebuild' ) {

            $entries = SearchRegister::where( 'index_version', '<', 1 );

            if ( $mode == 'create' ) {

                $entries = $entries->take( $limit );

            }

            $entries = $entries->get();

            foreach ( $entries as $entry ) {

                SearchIndex::createIndex( $entry->id );

            }

        }

    }

}
