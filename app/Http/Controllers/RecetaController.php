<?php

namespace App\Http\Controllers;

use App\Models\CategoriaReceta;
use App\Models\Receta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RecetaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Auth::user()->recetas->dd();

        $recetas = auth()->user()->recetas;

        return view('recetas.index')->with('recetas',$recetas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #Obtener categorías sin modeloo
        //$categorias = DB::table('categoria_recetas')->get()->pluck('nombre','id');
        #Con modelo
        $categorias = CategoriaReceta::all('id','nombre');
        return view(('recetas.create'))->with('categorias',$categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd( $request['imagen']->store('upload-recetas', 'public'));

        #Validación
        $data = $request->validate([
            'titulo' => 'required|min:6',
            'preparacion' => 'required',
            'ingredientes' => 'required',
            'imagen' => 'required|image',
            'categoria' => 'required',
        ]);
        #Obtener ruta de la imagen
        $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');
        #Resize de la imagen
        $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000,550);
        $img->save();
        #Almacenar en la base de datos sin modelo
        /* DB::table('recetas')->insert([
            'titulo' => $data['titulo'],
            'preparacion' => $data['preparacion'],
            'ingredientes' => $data['ingredientes'],
            'imagen' => $ruta_imagen,
            'user_id' => Auth::user()->id,
            'categoria_id' => $data['categoria'],
        ]) */;
      
        #Almacenar en base de datos con modelo
        auth()->user()->recetas()->create([
            'titulo' => $data['titulo'],
            'preparacion' => $data['preparacion'],
            'ingredientes' => $data['ingredientes'],
            'imagen' => $ruta_imagen,
            'categoria_id' => $data['categoria'],
        ]);

        return redirect()->action([RecetaController::class, 'index']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function show(Receta $receta)
    {
        return view('recetas.show', compact('receta'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function edit(Receta $receta)
    {
        $categorias = CategoriaReceta::all('id','nombre');
        return view('recetas.edit', compact('categorias','receta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receta $receta)
    {
        $categorias = CategoriaReceta::all('id','nombre');
        $data = $request->validate([
            'titulo' => 'required|min:6',
            'preparacion' => 'required',
            'ingredientes' => 'required',
            'categoria' => 'required',
        ]);
        #Asignar los valores
        $receta->titulo = $data['titulo'];
        $receta->preparacion = $data['preparacion'];
        $receta->ingredientes = $data['ingredientes'];
        $receta->categoria_id = $data['categoria'];

        #Si el usuario sube una nueva imagen
        if(request('imagen')){
            $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');
            $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000, 550);
            $img->save();
            #Asignar al objeto
            $receta->imagen = $ruta_imagen;
        }
        $receta->save();

        #Redireccionar
        return redirect()->action([RecetaController::class, 'index']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receta $receta)
    {
        #Ejecutar el Policy
        $this->authorize('delete',$receta);
        #Eliminar la receta
        $receta->delete();
        return redirect()->action([RecetaController::class, 'index']);
    }
}
