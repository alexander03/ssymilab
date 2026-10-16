<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DocumentDetail;
use App\Models\ReferralGuide;
use Illuminate\Http\Request;

class ReferralGuideController extends Controller
{
    protected $directory = 'app.store.referral-guide';
    protected $title_admin = 'Guías de remisión';
    protected $title_show = 'Ver guia de remisión';
    protected $title_create = 'Crear guia de remisión';
    protected $title_edit = 'Editar guia de remisión';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Guia de remisión';
    protected $option_create = 'Crear guia de remisión';
    protected $option_show = 'Ver guia de remisión';
    protected $option_edit = 'Editar guia de remisión';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //sleep(5);
        $title_admin = $this->title_admin;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;

        return view("{$this->directory}.admin")->with(compact('title_admin', 'category_menu', 'option_menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //sleep(4);
        $title_create = $this->title_create;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_create = $this->option_create;

        $referral_guide = new ReferralGuide();

        return view("{$this->directory}.create")->with(compact('referral_guide', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($referral_guide_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $referral_guide = ReferralGuide::findOrFail($referral_guide_id);
            $details = $referral_guide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('referral-guide');
        }

        return view("{$this->directory}.show")->with(compact('referral_guide', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $store_document
     * @return \Illuminate\Http\Response
     */
    // public function edit($store_document_id)
    // {
    //     $title_edit = $this->title_edit;
    //     $category_menu = $this->category_menu;
    //     $option_menu = $this->option_menu;
    //     $option_edit = $this->option_edit;

    //     try {
    //         $store_document = StoreDocument::findOrFail($store_document_id);
    //         if($store_document->status == "Y" || $store_document->status == "A"){
    //             Session::flash('warning', 'El documento de compra se encuentra anulado o con despachos!');
    //             return redirect()->route('store_document');
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->route('store_document');
    //     }

    //     return view("{$this->directory}.edit")->with(compact('store_document', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    // }
}
