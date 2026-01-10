<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // проверяем, поддерживаемый ли язык
        if (in_array($locale, ['en','ru','az'])) {
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }
}
