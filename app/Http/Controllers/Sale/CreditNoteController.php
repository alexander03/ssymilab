<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\SaleGuide;
use App\Models\Sale;
use App\Models\CreditNote;
use App\Models\DocumentDetail;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    protected $directory = 'app.sale.creditnote';
    protected $title_admin = 'Nota Credito';
    protected $title_show = 'Ver Nota Credito';
    protected $title_create = 'Crear Nota Credito';
    protected $title_edit = 'Editar Nota Credito';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Nota Credito';
    protected $option_create = 'Crear Nota Credito';
    protected $option_show = 'Ver Nota Credito';
    protected $option_edit = 'Editar Nota Credito';

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

        $creditnote = new CreditNote();

        return view("{$this->directory}.create")->with(compact('creditnote', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($creditnote_id)
    {
        //sleep(4);
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $sale = CreditNote::findOrFail($creditnote_id);
            $details = $sale->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves($value->id);
                $details[$key]->product_moves = $product_moves;
            }
            $documents = [];

            if($sale->document){
                $documents["document"] = $sale->document;
            }


        } catch (\Exception $e) {
            return redirect()->route('sale');
        }

        return view("{$this->directory}.show")->with(compact('sale', 'title_show', 'category_menu', 'option_menu', 'option_show', 'documents'));
    }

}
