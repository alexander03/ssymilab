<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\SaleGuide;
use App\Models\Sale;
use App\Models\DocumentDetail;
use App\Models\Document;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Traits\Sale\SaleTrait;

class SaleController extends Controller
{
    use SaleTrait;

    protected $directory = 'app.sale.sale';
    protected $title_admin = 'Ventas';
    protected $title_show = 'Ver venta';
    protected $title_create = 'Crear venta';
    protected $title_edit = 'Editar venta';
    protected $category_menu = 'Ventas';
    protected $option_menu = 'Venta';
    protected $option_create = 'Crear venta';
    protected $option_show = 'Ver venta';
    protected $option_edit = 'Editar venta';

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

        $sale = new Sale();

        return view("{$this->directory}.create")->with(compact('sale', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($sale_id)
    {
        //sleep(4);
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $sale = Sale::findOrFail($sale_id);
            $details = $sale->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves($value->id);
                $details[$key]->product_moves = $product_moves;
            }
            $sale_guides = SaleGuide::where('document_id', $sale->id)->where('status', '<>', 'A')->get();
            $documents = [];

            if($sale->document){
                $documents["document"] = $sale->document;
            }

            if(count($sale_guides) > 0){
                $documents["sale_guides"] = $sale_guides;
            }

        } catch (\Exception $e) {
            return redirect()->route('sale');
        }

        return view("{$this->directory}.show")->with(compact('sale', 'title_show', 'category_menu', 'option_menu', 'option_show', 'documents'));
    }

    public function excel(Request $request)
    {
        $lista = $this->search_sales($request->input('date_start'),$request->input('date_end'),$request->input('document_type_id'), $request->input('number'),$request->input('supplier'), $request->input('vendedor'),$request->input('status_sale'), null, 'documents.created_at', 'asc', 1000000);

        //GUIAS DE VENTA (16) Y GUIAS DE REMISION (8) DE CADA VENTA, EN UNA SOLA CONSULTA POR BLOQUE
        $guides = collect();
        foreach (array_chunk(collect($lista->items())->pluck('id')->toArray(), 5000) as $sale_ids) {
            $guides = $guides->merge(
                Document::whereIn('document_id', $sale_ids)
                    ->whereIn('document_type_id', [8, 16])
                    ->orderBy('number', 'asc')
                    ->get(['document_id', 'document_type_id', 'number', 'status'])
            );
        }
        $guides = $guides->groupBy('document_id');

        return view("{$this->directory}.excel")->with(compact('lista', 'guides'));
    }

    public function anularPago(Request $request)
    {
        $sale_id = $request->input('sale_id');
        $payment_id = $request->input('payment_id');
        $documentpayment_id = $request->input('documentpayment_id');
        
        try {
            $respuesta = $this->anularPagoTrait($sale_id,$documentpayment_id,$payment_id);
            return response()->json(['success' => true, 'message' => 'Pago anulado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al anular el pago: ' . $e->getMessage()]);
        }
    }
}
