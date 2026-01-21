<?php

namespace App\Jobs;

use App\Services\FlexiDbProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFlexiDbJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1500;

    public function __construct(
        public string $dateYmd,
        public string $requestId
    ) {
    }

    public function handle(FlexiDbProcessor $processor): void
    {
        Log::info('ProcessFlexiDbJob started', [
            'requestId' => $this->requestId,
            'date' => $this->dateYmd,
        ]);

        $result = $processor->process($this->dateYmd);

        Log::info('ProcessFlexiDbJob finished', [
            'requestId' => $this->requestId,
            'result' => $result,
        ]);
    }
}


