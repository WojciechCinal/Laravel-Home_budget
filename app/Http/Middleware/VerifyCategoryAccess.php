<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class VerifyCategoryAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $categoryId = $request->route('id');
        $user = Auth::user();

        $category = Category::where('id_category', $categoryId)
            ->where('id_user', $user->id_user)
            ->first();

        if (!$category) {
            return redirect()->route('home')->with('error', 'Nie masz dostępu do tej strony!');
        }

        return $next($request);
    }
}
