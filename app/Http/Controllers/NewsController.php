<?php
namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function edit()
    {
        $news = News::findOrFail(1);
        return view('news.edit', compact('news'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'telegram' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:255',
        ]);

        $news = News::findOrFail(1);
        $news->telegram = $request->input('telegram');
        $news->whatsapp = $request->input('whatsapp');

        if ($news->save()) {
            return redirect()->route('news.edit')->with('success', 'Success, News has been updated.');
        } else {
            return redirect()->route('news.edit')->with('error', 'Sorry, something went wrong while updating the News.');
        }
    }
}
