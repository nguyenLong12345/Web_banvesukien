<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscribeController extends Controller
{
    /**
     * Handle newsletter subscription (matches original subscribe.php behavior).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email không hợp lệ.',
            ]);
        }

        // TODO: Store in subscribers table when migration exists.
        // For now, return success to match expected behavior.
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký nhận thông tin thành công! Cảm ơn bạn.',
        ]);
    }
}
