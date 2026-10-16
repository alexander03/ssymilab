<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DocumentDetail;
use App\Models\StoreDocument;
use Illuminate\Http\Request;
use App\Imports\DocAlmacenImport;
use Illuminate\Support\Facades\DB;
use Excel;
use Barryvdh\DomPDF\Facade as PDF;

class StoreDocumentController extends Controller
{
    protected $directory = 'app.store.store-document';
    protected $title_admin = 'Documentos de almacén';
    protected $title_show = 'Ver documento de almacén';
    protected $title_create = 'Crear documento de almacén';
    protected $title_edit = 'Editar documento de almacén';
    protected $category_menu = 'Almacén';
    protected $option_menu = 'Documento de almacén';
    protected $option_create = 'Crear documento de almacén';
    protected $option_show = 'Ver documento de almacén';
    protected $option_edit = 'Editar documento de almacén';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $title_create = $this->title_create;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_create = $this->option_create;

        $store_document = new StoreDocument();

        return view("{$this->directory}.create")->with(compact('store_document', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }

    /**
     * Show a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($store_document_id)
    {
        $title_show = $this->title_show;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_show = $this->option_show;

        try {
            $store_document = StoreDocument::findOrFail($store_document_id);
            $details = $store_document->details;
            foreach ($details as $key => $value) {
                $product_moves = DocumentDetail::product_moves_guide($value->id);
                $details[$key]->product_moves = $product_moves;
            }
        } catch (\Exception $e) {
            return redirect()->route('store-document');
        }

        return view("{$this->directory}.show")->with(compact('store_document', 'title_show', 'category_menu', 'option_menu', 'option_show'));
    }

    /**
     * Funcion para importar.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     */
    public function saveimport(Request $request)
    {
        $error = DB::transaction(function () use ($request) {
            if ($request->hasFile("file")) {
                $file = $request->file("file");
                if ($file->getClientMimeType() == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $file->getClientMimeType() == "application/vnd.ms-excel" || $file->getClientMimeType() == "application/octet-stream") {
                    session(['import_docalmacen_sucursal_id' => $request->input("sucursal_id")]);
                    $namefile = time() . $file->getClientOriginalName();
                    $file->move(public_path() . '/imports/', $namefile);
                    $data = Excel::import(new DocAlmacenImport, public_path() . '/imports/' . $namefile);
                } else {
                    echo json_encode(array("file" => array("El archivo debe ser formato xls o xlsx")));
                    exit();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $proforma
     * @return \Illuminate\Http\Response
     */
    public function pdf($store_document)
    {
        $store_document = StoreDocument::findOrFail($store_document);
        $details = $store_document->details;
        foreach ($details as $key => $value) {
            $product_moves = DocumentDetail::product_moves_guide($value->id);
            $details[$key]->product_moves = $product_moves;
        }
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('store_document'));
        return $pdf->stream("documento-almacen.pdf");
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
