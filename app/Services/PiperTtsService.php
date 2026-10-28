<?php

namespace App\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class PiperTtsService
{
    public function synthesize(string $text, string $voice = 'google-medium'): string
    {
        $models = [
            'google-medium' => 'ne_NP-google-medium.onnx',
            'chitwan-medium' => 'ne_NP-chitwan-medium.onnx',
        ];

        if (! isset($models[$voice])) {
            throw new RuntimeException('Unsupported Nepali Piper voice.');
        }

        $model = storage_path('app/piper/' . $models[$voice]);

        if (! is_file($model)) {
            throw new RuntimeException('Nepali Piper voice model is not installed.');
        }

        $outputFile = tempnam(storage_path('app/piper'), 'tts_');

        if ($outputFile === false) {
            throw new RuntimeException('Could not create a temporary speech file.');
        }

        try {
            $process = new Process([
                env('PIPER_PYTHON', 'python'),
                '-m',
                'piper',
                '--model',
                $model,
                '--output-file',
                $outputFile,
            ], base_path());

            $process->setEnv([
                'PYTHONUTF8' => '1',
                'PYTHONIOENCODING' => 'utf-8',
            ]);

            // Piper 1.x expects newline-delimited JSON input, not raw text.
            $process->setInput(json_encode([
                'text' => $text,
            ], JSON_UNESCAPED_UNICODE) . PHP_EOL);
            $process->setTimeout(30);
            $process->run();

            if (! $process->isSuccessful()) {
                throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Piper could not generate speech.');
            }

            $audio = file_get_contents($outputFile);

            if ($audio === false || substr($audio, 0, 4) !== 'RIFF') {
                throw new RuntimeException('Piper returned invalid audio.');
            }

            return $audio;
        } finally {
            @unlink($outputFile);
        }
    }
}
