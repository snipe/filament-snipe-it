<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\HttpFoundation\Response;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;


class CheckForDebug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (((auth()->check() && (auth()->user()->isSuperUser()))) && (app()->environment() == 'production') && (config('app.warn_debug') === true) && (config('app.debug') === true)) {

            FilamentView::registerRenderHook(
                PanelsRenderHook::CONTENT_START,
                fn (): string => Blade::render('debugmode-warning'),
            );
        }

        return $next($request);
    }
}
