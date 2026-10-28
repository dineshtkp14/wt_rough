<?php

namespace App\Http\Controllers;

use App\Services\PiperTtsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PiperTtsController extends Controller
{
    public function speak(Request $request, PiperTtsService $piper): Response
    {
        $text = trim((string) $request->input('text', 'यो ग्राहकको नगद बिल हो। कुल रकम एक हजार रुपैयाँ हो।'));
        $voice = (string) $request->input('voice', 'google-medium');
        $text = mb_substr($text, 0, 500);

        if ($text === '') {
            return response('Text is required.', 422);
        }

        try {
            return response($piper->synthesize($text, $voice), 200, [
                'Content-Type' => 'audio/wav',
                'Cache-Control' => 'no-store',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response('Nepali speech could not be generated: ' . $exception->getMessage(), 500);
        }
    }
}
