<?php

namespace Patrick\LanguagePanel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine;

class ImportFromLangFiles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param mixed $overwrite
     * @param mixed $truncate
     */
    public function __construct(
        public $overwrite,
        public $truncate,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->truncate) {
            LanguageLine::truncate();
        }

        $langDirs = File::directories(lang_path());

        foreach ($langDirs as $langDir) {
            $dirName = basename($langDir);
            if (
                'vendor' == $dirName
                && config('language-panel.import.add_vendor', false)
            ) {
                foreach (File::directories($langDir) as $vendorDir) {
                    $packageName = basename($vendorDir);

                    $langfiles = File::allFiles($vendorDir);

                    foreach ($langfiles as $langFile) {
                        $content = File::getRequire($langFile->getPathname());

                        $this->parseFileContents(
                            $content,
                            $langFile,
                            $langFile->getRelativePath(),
                            $packageName,
                        );
                    }
                }
            } else {
                $langfiles = File::allFiles($langDir);

                foreach ($langfiles as $langFile) {
                    $content = File::getRequire($langFile->getPathname());

                    $this->parseFileContents($content, $langFile, $dirName);
                }
            }
        }
    }

    /**
     * Check if language file contents are an array, if so,
     * start processing the contents.
     */
    private function parseFileContents(
        array|int $content,
        object $langFile,
        string $localeName,
        ?string $groupNamePrefix = null,
    ): void {
        if (is_array($content)) {
            foreach (Arr::dot($content) as $key => $value) {
                if (is_string($value)) {
                    $groupName = Str::of($langFile->getRelativePathname())
                        ->remove('.php')
                    ;

                    if ($groupNamePrefix) {
                        $groupName = $groupNamePrefix . '::' . basename($groupName);
                    }

                    $this->createOrUpdateLine(
                        group: $groupName,
                        key: $key,
                        langName: $localeName,
                        value: $value,
                    );
                }
            }
        }
    }

    /**
     * If a language file exists, and overwrite is on, update it.
     * If override is off, only overwrite it when it is empty.
     *
     * If it does not exist, create it.
     */
    private function createOrUpdateLine(
        string $group,
        string $key,
        string $langName,
        string $value,
    ): void {
        $langguageLine = LanguageLine::query()
            ->where('group', '=', $group)
            ->where('key', '=', $key)
            ->first()
        ;

        if ($langguageLine) {
            $text = $langguageLine->text;
            if ($this->overwrite) {
                $text[$langName] = $value;
                $langguageLine->update(['text' => $text]);
            } else {
                if (array_key_exists($langName, $text) && empty($text[$langName])) {
                    $text[$langName] = $value;
                    $langguageLine->update(['text' => $text]);
                }
                if (! array_key_exists($langName, $text)) {
                    $text[$langName] = $value;
                    $langguageLine->update(['text' => $text]);
                }
            }
        } else {
            LanguageLine::create([
                'group' => $group,
                'key' => $key,
                'text' => [$langName => $value],
            ]);
        }
    }
}
