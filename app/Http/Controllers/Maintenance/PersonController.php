<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\PersonProduct;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Traits\Maintenance\PersonTrait;

class PersonController extends Controller
{
    use PersonTrait;

    protected $directory = 'app.maintenance.person';
    protected $title_admin = 'Personas';
    protected $title_create = 'Crear persona';
    protected $title_edit = 'Editar persona';
    protected $category_menu = 'Mantenimiento';
    protected $option_menu = 'Personas';
    protected $option_create = 'Crear persona';
    protected $option_edit = 'Editar persona';

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

        $person = new Person();

        return view("{$this->directory}.create")->with(compact('person', 'title_create', 'category_menu', 'option_menu', 'option_create'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $person
     * @return \Illuminate\Http\Response
     */
    public function edit($person_id)
    {
        $title_edit = $this->title_edit;
        $category_menu = $this->category_menu;
        $option_menu = $this->option_menu;
        $option_edit = $this->option_edit;

        try {
            $person = Person::findOrFail($person_id);
        } catch (\Exception $e) {
            return redirect()->route('person');
        }

        return view("{$this->directory}.edit")->with(compact('person', 'title_edit', 'category_menu', 'option_menu', 'option_edit'));
    }

    public function pdf($id,Request $request)
    {
        $lista = $this->search_people($request->input('name'), $request->input('document_number'), $request->input('role_id'), 'name_complete', 'asc', 10000);
        $pdf = PDF::loadView("{$this->directory}.pdf", compact('lista'))->setPaper('A4', 'Portrait');
        return $pdf->stream("people.pdf");
    }

    public function migrar(Request $request)
    {
        $lista = PersonProduct::where('person_id','=',174)->get();
        foreach ($lista as $key => $item) {
            PersonProduct::updateOrCreate([
                'person_id' => 75,
                'product_id' => $item->product_id,
                'price' => $item->price,
            ]);

            PersonProduct::updateOrCreate([
                'person_id' => 78,
                'product_id' => $item->product_id,
                'price' => $item->price,
            ]);

            PersonProduct::updateOrCreate([
                'person_id' => 79,
                'product_id' => $item->product_id,
                'price' => $item->price,
            ]);
        }
    }
}
