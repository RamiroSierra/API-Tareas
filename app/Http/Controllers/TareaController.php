<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function ListarTodos(Request $request)
    {
        $tareas = Tarea::all();
        return $tareas;
    }

    public function Buscar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        return $tarea;
    }

    public function Crear(Request $request)
    {
        $tarea = new Tarea();
        $tarea->titulo = $request->post('titulo');
        $tarea->autor_id = $request->post('autor_id');
        $tarea->asignado_id = $request->post('asignado_id');
        $tarea->cuerpo = $request->post('cuerpo');
        $tarea->fecha_expiracion = $request->post('fecha_expiracion');
        $tarea->categorias = $request->post('categorias');
        $tarea->save();

        return $tarea;
    }

    public function Modificar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->titulo = $request->post('titulo');
        $tarea->asignado_id = $request->post('asignado_id');
        $tarea->cuerpo = $request->post('cuerpo');
        $tarea->fecha_expiracion = $request->post('fecha_expiracion');
        $tarea->categorias = $request->post('categorias');
        $tarea->save();

        return $tarea;
    }

    public function Eliminar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return response()->json(['deleted' => true]);
    }

    public function AgregarComentario(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        
        $comentarios = $tarea->comentarios ?? [];
        $comentarios[] = [
            'usuario_id' => $request->post('usuario_id'),
            'texto' => $request->post('texto'),
            'fecha' => now()->toDateTimeString()
        ];
        
        $tarea->comentarios = $comentarios;
        $tarea->save();

        return $tarea;
    }
}