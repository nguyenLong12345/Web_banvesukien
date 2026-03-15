<?php

namespace App\Services\Auth;

use App\Models\User;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;

class FirebaseService
{
    protected ?Auth $auth = null;

    protected function getAuth(): Auth
    {
        if ($this->auth === null) {
            $credentials = config('services.firebase.credentials');
            // If the path is relative, resolve it against the Laravel base path
            if (is_string($credentials) && !empty($credentials)) {
                if (!file_exists($credentials)) {
                    $credentials = base_path($credentials);
                }
                if (file_exists($credentials)) {
                    $this->auth = (new Factory)->withServiceAccount($credentials)->createAuth();
                } else {
                    throw new \RuntimeException('Firebase credentials file not found at: ' . $credentials . '. Set FIREBASE_CREDENTIALS in .env.');
                }
            } else {
                throw new \RuntimeException('Firebase credentials file not found. Set FIREBASE_CREDENTIALS in .env.');
            }
        }

        return $this->auth;
    }

    /**
     * Verify Firebase idToken and return payload (uid, email).
     *
     * @return array{uid: string, email: string, fullname?: string}|null
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            $verifiedIdToken = $this->getAuth()->verifyIdToken($idToken);
            $payload = $verifiedIdToken->claims()->all();
            $uid = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;

            if (empty($uid) || empty($email)) {
                return null;
            }

            $fullname = $payload['name'] ?? explode('@', $email)[0];

            return [
                'uid' => $uid,
                'email' => $email,
                'fullname' => $fullname,
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Firebase Token Verification Failed: ' . $e->getMessage(), [
                'token' => substr($idToken, 0, 20) . '...' // Log a small part of token for context
            ]);
            return null;
        }
    }

    /**
     * Find or create user by Firebase UID.
     */
    public function findOrCreateUser(string $uid, string $email, ?string $fullname = null): User
    {
        $fullname = $fullname ?? explode('@', $email)[0];

        $user = User::where('firebase_uid', $uid)->first();

        if ($user) {
            if ($user->fullname !== $fullname) {
                $user->update(['fullname' => $fullname]);
            }

            return $user->fresh();
        }

        do {
            $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $newUserId = 'PKA'.$rand;
            $exists = User::where('user_id', $newUserId)->exists();
        } while ($exists);

        return User::create([
            'user_id' => $newUserId,
            'fullname' => $fullname,
            'email' => $email,
            'password' => '',
            'reset_token' => '',
            'reset_expire' => null,
            'firebase_uid' => $uid,
        ]);
    }
}
