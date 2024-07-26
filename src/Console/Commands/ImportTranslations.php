<?php

namespace Patrick\LanguagePanel\Console\Commands;

use Illuminate\Console\Command;
use Patrick\LanguagePanel\Jobs\ImportFromLangFiles;

class ImportTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language-panel:import
    {--O|overwrite : Overwrite existing language lines with those in the lang/ dir}
    {--T|truncate : Truncate the database table before loading}
    {--F|force : do not prompt for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations from language files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overwriteAllowed = config('language-panel.import.allow_overwrite', false);
        $truncateAllowed = config('language-panel.import.allow_truncate', false);

        $overwrite = $this->option('overwrite');
        $truncate = $this->option('truncate');

        if ($this->option('force')) {
            ImportFromLangFiles::dispatchSync($overwrite, $truncate);
        } elseif ($truncate && $truncateAllowed) {
            $truncateConfirmed = $this->confirm('Are you sure you want to truncate the database table?', false);

            ImportFromLangFiles::dispatchSync($overwrite, $truncateConfirmed);
        } elseif ($overwrite && $overwriteAllowed) {
            $overwriteConfirmed = $this->confirm('Are you sure you want to overwrite existing translations?', false);

            ImportFromLangFiles::dispatchSync($truncate, $overwriteConfirmed);
        } else {
            ImportFromLangFiles::dispatchSync($overwrite, $truncate);
        }

        $this->info('Done!');

        return 1;
    }
}
