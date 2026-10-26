<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function getRegistrationArticles() : View
    {
        $localLang = app()->getLocale();
        $articles = Article::query()
            ->join('article_translations as b', 'b.article_id', '=', 'articles.id')
            ->where('articles.category_id', Article::CATEGORY_INSURANCE)
            ->where('articles.status_in_tpl', Article::STATUS_ACTIVE)
            ->where('b.lang', $localLang)
            ->select('articles.tin', 'articles.file', 'articles.email', 'articles.phone', 'b.title', 'b.link', 'b.address')
            ->inRandomOrder()
            ->get();
        $articles = $articles->map(function ($article) {
            $article->title = str_replace(" Açıq Səhmdar Cəmiyyəti", " ASC", $article->title);
            return $article;
        });

        return view('registration', ['articles' => $articles]);
    }

    public function getContactArticles() : View
    {
        $localLang = app()->getLocale();

        $articles = Article::query()
            ->join('article_translations as b', 'b.article_id', '=', 'articles.id')
            ->where('articles.category_id', Article::CATEGORY_INSURANCE)
            ->where('articles.status_in_tpl', Article::STATUS_ACTIVE)
            ->where('b.lang', $localLang)
            ->select('articles.tin', 'articles.file', 'articles.email', 'articles.phone', 'b.title', 'b.link', 'b.address')
            ->get();
        $articles = $articles->map(function ($article) {
            $article->title = str_replace(" Açıq Səhmdar Cəmiyyəti", " ASC", $article->title);
            return $article;
        });

        return view('insurance-companies', ['articles' => $articles]);
    }
    public function getCompanyLogosForIndex() : View
    {
        $localLang = app()->getLocale();
        $logos = Article::query()
            ->join('article_translations as b', 'b.article_id', '=', 'articles.id')
            ->where('articles.category_id', Article::CATEGORY_INSURANCE)
            ->where('articles.status_in_tpl', Article::STATUS_ACTIVE)
            ->where('b.lang', $localLang)
            ->select('articles.file', 'b.title', 'b.link', 'articles.tin')
            ->distinct('articles.tin')
            ->inRandomOrder()
            ->get();
            $uniqueLogos = $logos->unique('tin')->values();
        return view('index', ['logos' => $uniqueLogos]);
    }
    public function faq(): View
    {
        $faqs = [];

        for ($i = 1; $i <= 14; $i++) {
            $faqs[] = [
                'question' => __("messages.faq_question_$i"),
                'answer'   => __("messages.faq_answer_$i"),
            ];
        }

        return view('faq', ['faqs' => $faqs]);
    }

    public function status(Request $request,$transId){
        return view('payment-status', ['transactionId' => $transId]);
    }

}
