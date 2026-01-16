<?php

namespace App\Services;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use Illuminate\Support\Facades\Http;

class AiService
{
    // Mock AI response for now
    public function generateResponse(AiChatSession $session, string $userMessage)
    {
        // In real app: call OpenAI/Anthropic/Gemini
        return "I am an AI Astrologer. You asked: $userMessage. Based on your chart...";
    }
}
