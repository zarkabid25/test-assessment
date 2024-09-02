<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function invite(InvitationRequest $request)
    {
        try {
            $token = Str::random(32);

            $invitation = Invitation::create([
                'email' => $request->email,
                'token' => $token,
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            Log::info('Invitation Token: ' . $token);

            return response()->json([
                'status' => 200,
                'message' => 'Invitation sent.',
                'data' => []
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to send invitation.',
                'data' => ['error' => $th->getMessage()]
            ], 400);
        }
    }

    public function resendInvite(InvitationRequest $request)
    {
        try {
            $invitation = Invitation::where('email', $request->email)->first();

            if (!$invitation) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Invitation not found.',
                    'data' => []
                ], 404);
            }

            if ($invitation->is_used || $invitation->expires_at < Carbon::now()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invitation is no longer valid.',
                    'data' => []
                ], 400);
            }

            $token = Str::random(32);
            $invitation->update([
                'token' => $token,
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            Log::info('Resent Invitation Token: ' . $token);

            return response()->json([
                'status' => 200,
                'message' => 'Invitation resent.',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to resend invitation.',
                'data' => ['error' => $th->getMessage()]
            ], 400);
        }
    }
}
