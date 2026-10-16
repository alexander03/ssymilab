<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\SaleGuide;
use App\Models\DocumentDetail;
use App\Models\Sale;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SaleGuideController extends Controller
{
    protected $directory = 'app.sale.sale-guide';
    protected $title_admin = 'Guia de Venta';
    protected $title_show = 'Ver guia de venta';
    protected $title_create = 'Crear guia de venta';
    protected $title_edit = 'Editar guia de venta';
    protected $category_menu = 'Guia de Venta';
    protected $option_menu = 'Guia de Venta';
    protected $option_create = 'Crear Guia de venta';
    protected $option_show = 'Ver guia de venta';
    protected $option_edit = 'Editar guia de venta';

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
    public function create(Request $request)
    {
        //sleep(4);
        if(!is_null($request->input('sale_id'))){
            $sale_id = $request->input('sale_id');
        }else{
            $sale_id = 0;
        }
        $title_create = $this->title_create;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_create = $this->option_create;

        $sale_guide = new SaleGuide();

        return view("{$this->directory}.create")->with(compact('sale_guide', 'title_create', 'category_menu', 'option_menu', 'option_create','sale_id'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($sale_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $sale_guide = SaleGuide::findOrFail($sale_id);
            $sale = Sale::where('id', $sale_guide->document_id)->where('status', '<>', 'A')->get();
            $documents = [];
            $details = $sale_guide->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id,$sale_id);
                $details[$key]->product_moves = $product_moves;
            }

        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('sale-guide');
        }

        return view("{$this->directory}.show")->with(compact('sale','sale_guide', 'title_show', 'category_menu', 'option_menu', 'option_show', 'documents'));
    }

}
