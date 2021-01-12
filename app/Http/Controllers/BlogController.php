<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Navbar;
use App\Models\Banner;
use App\Models\BannerHeader;
use App\Models\Footer;
use App\Models\Tag;
use App\Models\Categorie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $navbars = Navbar::all();
        $banners = Banner::all();
        $bannerHeader = BannerHeader::all();
        $footers = Footer::all();
        $tags = Tag::all();
        $categories = Categorie::all();
        $blogArticles = Blog::all();

        $paginationArticles = Blog::orderBy('id', 'DESC')->paginate(1);

        return view('labs.blog', 
        compact(
        'navbars', 
        'banners', 
        'bannerHeader', 
        'footers', 
        'tags', 
        'categories', 
        'blogArticles',
        ))->with('pagination', $paginationArticles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        //
    }

    public function adminShowBannerHeaderBlog(Blog $blog) 
    {
        $bannerHeader = BannerHeader::all();
        return view('admin.blog.banniere', compact('bannerHeader'));
    }

    public function adminUpdateBannerHeaderBlog(Blog $blog, Request $request, $id) 
    {
        $updateBannerHeaderService = BannerHeader::find($id);

        $request->validate([
            'title' => 'min:5',
        ]);

        $updateBannerHeaderService->title = $request->title;
        $updateBannerHeaderService->lienPrecedent = $request->lienPrecedent;
        $updateBannerHeaderService->lienActuel = $request->lienActuel;
        $updateBannerHeaderService->save();
        return redirect()->back()->with('success', 'Modification effectué avec succès !');
    }
    
    public function adminShowArticles(Blog $blog) 
    {
        $articles = Blog::all();
        $tags = Tag::all();
        $categories = Categorie::all();
        return view('admin.blog.articles', compact('articles', 'tags', 'categories'));
    }

    public function adminCreateArticle(Blog $blog, Request $request) 
    {
        $storeArticle = new Blog();
        $storeArticle->image = $request->file('newImageArticle')->hashName();
        $storeArticle->date = $request->newDate;
        $storeArticle->titre = $request->newTitre;
        $storeArticle->auteur = $request->newAuteur;
        $storeArticle->texte = $request->newTexte;
        $storeArticle->photo_profil = $request->file('newPhotoProfil')->hashName();
        $storeArticle->texte_auteur = $request->newTexteAuteur;
        $storeArticle->fonction = $request->newFonction;
        $storeArticle->description = $request->newDescription;
        $storeArticle->save();
        $storeArticle->categorie()->syncWithoutDetaching($request->cats);
        $storeArticle->tag()->syncWithoutDetaching($request->tags);
        $request->file('newImageArticle')->storePublicly('img/blog/', 'public');
        $request->file('newPhotoProfil')->storePublicly('img/blog/', 'public');
        return redirect()->back()->with('success', 'Ajout effectué avec succès !');
    }

    public function adminShowEditArticle(Blog $blog, $id) 
    {
        $editArticle = Blog::find($id);
        return view('admin.blog.articleEdit', compact('editArticle'));
    }

    public function adminUpdateArticle(Blog $blog, Request $request, $id) 
    {
        $updateArticle = Blog::find($id);

        if ($updateArticle->image != 'blog-2.jpg') {
            Storage::disk('public')->delete('img/blog/' . $updateArticle->image);
        }

        $updateArticle->image = $request->file('changeImageArticle')->hashName();
        $updateArticle->date = $request->changeDate;
        $updateArticle->titre = $request->changeTitre;
        $updateArticle->auteur = $request->changeAuteur;
        $updateArticle->texte = $request->changeTexte;
        $updateArticle->photo_profil = $request->file('changePhotoProfil')->hashName();
        $updateArticle->texte_auteur = $request->changeTexteAuteur;
        $updateArticle->fonction = $request->changeFonction;
        $updateArticle->description = $request->changeDescription;
        $updateArticle->save();
        $request->file('changeImageArticle')->storePublicly('img/blog/', 'public');
        $request->file('changePhotoProfil')->storePublicly('img/blog/', 'public');
        return redirect('/articles');
    }

    public function adminDeletetArticle(Blog $blog, $id) 
    {
        $deleteArticle = Blog::find($id);
        $deleteArticle->delete();
        return redirect()->back();
    }

    public function adminShowCategories(Blog $blog) 
    {
        $categories = Categorie::all();
        return view('admin.blog.categories', compact('categories'));
    }

    public function adminCreateCategorie(Blog $blog, Request $request) 
    {
        $newCategorie = new Categorie();
        $newCategorie->nom = $request->nom;
        $newCategorie->save();
        return redirect()->back()->with('success', 'Ajout effectuée avec succès !');
    }

    public function adminEditCategorie(Blog $blog, $id) 
    {
        $editCategorie = Categorie::find($id);
        return view('admin.blog.categorieEdit', compact('editCategorie'));
    }

    public function adminUpdateCategorie(Blog $blog, Request $request, $id) 
    {
        $updateCategorie = Categorie::find($id);
        $updateCategorie->nom = $request->changeNom;
        $updateCategorie->save();
        return redirect()->back()->with('success', 'Modification effectuée avec succès !');
    }

    public function adminDeleteCategorie(Blog $blog, Request $request, $id) 
    {
        $updateCategorie = Categorie::find($id);
        $updateCategorie->delete();
        return redirect('/categories');
    }

    public function adminDeleteArticle(Blog $blog, $id) 
    {
        $deleteArticle = Blog::find($id);
        $deleteArticle->delete();
        return redirect()->back();
    }

    public function adminShowTags(Blog $blog) 
    {
        $tags = Tag::all();
        return view('admin.blog.tags', compact('tags'));
    }

    public function adminCreateTag(Blog $blog, Request $request) 
    {
        $newTag = new Tag();
        $newTag->nom = $request->nom;
        $newTag->save();
        return redirect()->back()->with('success', 'Ajout effectuée avec succès !');
    }

    public function adminEditTag(Blog $blog, $id) 
    {
        $editTag = Tag::find($id);
        return view('admin.blog.tagEdit', compact('editTag'));
    }

    public function adminUpdateTag(Blog $blog, Request $request, $id) 
    {
        $updateTag = Tag::find($id);
        $updateTag->nom = $request->changeTag;
        $updateTag->save();
        return redirect()->back()->with('success', 'Modification effectuée avec succès !');
    }

    public function adminDeleteTag(Blog $blog, Request $request, $id) 
    {
        $updateTag = Tag::find($id);
        $updateTag->delete();
        return redirect('/tags');
    }
}
