<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Maintenance\ProductTrait;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use ProductTrait;

    protected $directory = 'app.maintenance.product';
    protected $title_admin = 'Productos';
    protected $title_create = 'Crear producto';
    protected $title_edit = 'Editar producto';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Productos';
    protected $option_create = 'Crear producto';
    protected $option_edit = 'Editar producto';

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

        $product = new Product();

        return view("{$this->directory}.create")->with(compact('product', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $product
     * @return \Illuminate\Http\Response
     */
    public function edit($product_id)
    {
        //sleep(4);
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $product = Product::findOrFail($product_id);
        } catch (\Exception $e) {
            return redirect()->route('product');
        }

        return view("{$this->directory}.edit")->with(compact('product', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function download_file($id)
    {
        //COLOCAR SEGURIDAD SI ES ADMINISTRADOR O SI LO TIENE COMPRADO
        try {
            $product = Product::findOrFail($id);
            $media = $product->getMedia('images')[0];
            return response()->download($media->getPath(), $media->file_name);
        } catch (\Exception $e) {
            return redirect()->route('product');
        }
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($id,Request $request)
    {
        //sleep(4);

        $lista = $this->search_products($request->input('type'), $request->input('name'), $request->input('category_id'), $request->input('brand_id'), $request->input('supplier_id'), $request->input('status_product'), "products.name","asc", 100000);

        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("product-list.pdf");
    }

    //MISMOS FILTROS QUE EL PDF; UNA FILA POR UNIDAD CON PRECIOS Y % DE GANANCIA
    public function excel($id,Request $request)
    {
        $lista = $this->search_products($request->input('type'), $request->input('name'), $request->input('category_id'), $request->input('brand_id'), $request->input('supplier_id'), $request->input('status_product'), "products.name","asc", 100000);

        return view("{$this->directory}.excel")->with(compact('lista'));
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdfStock($id,Request $request)
    {
        //sleep(4);
        $lista = Product::leftjoin('stocks',function($sql){
                            $sql->on('stocks.product_id','=','products.id')->where('stocks.branch_office_id','=',Auth::user()->branch_office_id);
                        })
                        ->where('products.name','ILIKE','%'.$request->input('name').'%')
                        ->where('stocks.branch_office_id','=',Auth::user()->branch_office_id)
                        ->orderBy('products.brand_id', 'asc')
                        ->orderBy('products.name', 'asc')
                        ->select('products.*','stocks.real as stock');
        if($request->input('brand_id')!=""){
            $lista = $lista->where('products.brand_id','=',$request->input('brand_id'));
        }
        if($request->input('type')!=""){
            $lista = $lista->where('products.type','=',$request->input('type'));
        }
        $lista = $lista->get();
        $pdf = PDF::loadView("{$this->directory}.pdf-stock", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("product-stock.pdf");
    }  

    public function pdfPrice($id,Request $request)
    {
        //sleep(4);
        $lista = Product::leftjoin('stocks',function($sql){
                            $sql->on('stocks.product_id','=','products.id')->where('stocks.branch_office_id','=',Auth::user()->branch_office_id);
                        })
                        ->where('products.name','ILIKE','%'.$request->input('name').'%')
                        ->where('stocks.branch_office_id','=',Auth::user()->branch_office_id)
                        ->orderBy('products.brand_id', 'asc')
                        ->orderBy('products.name', 'asc')
                        ->select('products.*','stocks.real as stock');
        if($request->input('brand_id')!=""){
            $lista = $lista->where('products.brand_id','=',$request->input('brand_id'));
        }
        if($request->input('type')!=""){
            $lista = $lista->where('products.type','=',$request->input('type'));
        }
        $lista = $lista->get();
        $pdf = PDF::loadView("{$this->directory}.pdf-price", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("product-price.pdf");
    }  

    public function migrar(Request $request)
    {
        try {
            $list = Product::where('branch_office_id','=',1)->whereIn('id',[816])->orderBy('id','desc')->get();
            foreach ($list as $key => $value) {
	            $data = [
	            	'code' => $value->code,
			        'barcode' => $value->barcode,
			        'type' => $value->type,
			        'name' => $value->name,
			        'abbreviation' => $value->abbreviation,
			        'igv' => $value->igv,
			        'security_stock' => $value->security_stock,
			        'affects_stock' => $value->affects_stock,
			        'specifications' => $value->specifications,
			        'status' => $value->status,
			        'branch_office_id' => 3,
			        'brand_id' => $value->brand_id,
			        'category_id' => $value->category_id,
			        'supplier_id' => $value->supplier_id,
			        'base_unit_id' => $value->base_unit_id,
			        'base_currency_id' => $value->base_currency_id,
			        'presentation' => $value->presentation,
			        'model' => $value->model,
			        'rs' => $value->rs,
			        'condition' => $value->condition,
				    'source' => $value->source,
			        'currency_id' => $value->currency_id
			    ];
			    $product = Product::create($data);

			    $product_unit = ProductUnit::where('product_id','=',$value->id)->get();
			    foreach ($product_unit as $k => $v) {
				    ProductUnit::create([
		                'formula' => $v->formula,
		                'purchase_price' => $v->purchase_price,
		                'sell_price' => $v->sell_price,
		                'whosale_price' => $v->sell_price,
		                'branch_office_id' => $product->branch_office_id,
		                'unit_id' => $v->unit_id,
		                'product_id' => $product->id,
		                'base_unit_id' => $v->base_unit_id,
		                'currency_id' => $v->currency_id,
		            ]);
				}
            }
            return "ok";
        } catch (\Exception $e) {
            return redirect()->route('product');
        }

    } 

}
