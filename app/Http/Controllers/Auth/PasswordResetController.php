<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Password Reset",
 *     description="Endpoints for password reset functionality"
 * )
 */
class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/password-reset",
     *     summary="Send password reset link to user's email",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset link sent.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send reset link",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unable to send reset link.")
     *         )
     *     )
     * )
     */
    public function sendResetLinkEmail(SendResetLinkRequest $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent.'])
            : response()->json(['message' => 'Unable to send reset link.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Post(
     *     path="/api/user/password-reset/confirm",
     *     summary="Reset password using token",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="abcdef123456"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password has been reset.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token or request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token or request.")
     *         )
     *     )
     * )
     */
    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Invalid token or request.'], Response::HTTP_BAD_REQUEST);
    }
}