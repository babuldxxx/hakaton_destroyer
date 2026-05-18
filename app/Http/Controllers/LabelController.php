<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Label;
use Inertia\Inertia;

class LabelController extends Controller
{
    /**
     * Список всех артистов, принадлежащих лейблу текущего пользователя.
     * GET /label/artists
     */
    public function index()
    {
        $user = auth()->user();

        // Находим лейбл по label_id пользователя
        $label = Label::query()->find($user->label_id);

        if (!$label) {
            abort(403, 'У вас нет привязанного лейбла');
        }

        $artists = Artist::query()->where('label_id', $label->id)
            ->with('user') // подгружаем email, name пользователя
            ->get();

        return Inertia::render('Artists/Index', [
            'artists' => $artists,
            'label' => $label,
        ]);
    }
}
