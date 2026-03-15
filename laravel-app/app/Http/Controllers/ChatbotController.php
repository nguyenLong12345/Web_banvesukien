<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chat messages and return Gemini API response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            Log::error('Chatbot Error: Khuyết thiếu GEMINI_API_KEY trong file .env');
            return response()->json([
                'error' => 'Hệ thống AI hiện đang bảo trì. Vui lòng thử lại sau.'
            ], 500);
        }

        // 1. Fetch upcoming events to inject into prompt
        $upcomingEvents = Event::where('eStatus', 'Chưa diễn ra')
            ->where('start_time', '>=', now())
            ->take(5)
            ->get();

        $eventDataText = "THÔNG TIN CÁC SỰ KIỆN SẮP TỚI:\n";
        if ($upcomingEvents->isEmpty()) {
            $eventDataText .= "Hiện tại chưa có sự kiện nào sắp tới.\n";
        } else {
            foreach ($upcomingEvents as $event) {
                // Ensure price is formatted properly without decimals if it's an integer
                $formattedPrice = number_format($event->price, 0, ',', '.');
                $startTime = $event->start_time ? $event->start_time->format('d/m/Y H:i') : 'Chưa rõ';
                $eventDataText .= "- Tên: {$event->event_name} | Ngày: {$startTime} | Giá từ: {$formattedPrice} VNĐ | Địa điểm: {$event->location}\n";
            }
        }

        // Define a strict system prompt to constrain AI behavior
        $systemPrompt = "Bạn là trợ lý ảo hỗ trợ khách hàng cho Ticket Events, một nền tảng bán vé sự kiện trực tuyến tại Việt Nam (có các loại sự kiện như Âm nhạc, Văn hóa nghệ thuật, Tham quan, Giải đấu) ở các thành phố HN, HCM, Đà Lạt, Quảng Ninh, Huế, Đà Nẵng, Quảng Nam.
Nhiệm vụ của bạn là trả lời ngắn gọn, lịch sự, chuyên nghiệp bằng tiếng Việt và chỉ tập trung vào việc hỗ trợ mua vé, quản lý tài khoản, thông tin sự kiện.
Dưới đây là dữ liệu sự kiện hiện tại của hệ thống để bạn trả lời khách (nếu khách hỏi về sự kiện không có ở đây thì nói là hiện chưa có thông tin):

" . $eventDataText . "

Không trả lời các câu hỏi không liên quan đến hệ thống bán vé hoặc chủ đề sự kiện.
Trả về định dạng plain text hoặc markdown cơ bản, không được dùng HTML.";

        // List of Gemini models to try (fallback if one hits quota)
        $models = ['gemini-2.5-flash', 'gemini-2.5-pro'];

        try {
            foreach ($models as $model) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nCâu hỏi của khách hàng: " . $userMessage]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        $aiText = $data['candidates'][0]['content']['parts'][0]['text'];
                        return response()->json(['reply' => $aiText]);
                    }
                }

                // If rate limited (429), try next model
                if ($response->status() === 429) {
                    Log::warning("Chatbot: Model {$model} bị rate limit, thử model tiếp theo...");
                    continue;
                }

                // For other errors, log and break
                Log::error("Chatbot API Error ({$model}): " . $response->body());
                break;
            }

            return response()->json([
                'error' => 'Xin lỗi, tôi đang gặp trục trặc kỹ thuật. Vui lòng thử lại sau!'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Đã có lỗi xảy ra khi kết nối hệ thống AI.'
            ], 500);
        }
    }
}
