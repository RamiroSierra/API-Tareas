<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TareaController extends Controller
{
    public function __construct()
    {
        $this->historialApiUrl = env('HISTORIAL_API_URL', 'http://localhost:8002/api');
    }
    
    public function ListarTodos(Request $request)
    {
        $tareas = Tarea::all();
        return $tareas;
    }

    public function Buscar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        return response()->json($tarea);
    }

    public function Crear(Request $request)
    {   
        $user = $request->user;

        $tarea = new Tarea();
        $tarea->titulo = $request->post('titulo');
        $tarea->autor_id = $user['id'];
        $tarea->asignado_id = $request->post('asignado_id');
        $tarea->cuerpo = $request->post('cuerpo');
        $tarea->fecha_expiracion = $request->post('fecha_expiracion');
        $tarea->categorias = $request->post('categorias');
        $tarea->save();

        $this->registrarEnHistorial(
            $tarea->id, 
            'modificado',
            $tarea->titulo,
            $user['id']
        );

        return $tarea;
    }

    public function Modificar(Request $request, $id)
    {
        $user = $request->user;
        $tarea = Tarea::findOrFail($id);
        $tarea->titulo = $request->post('titulo', $tarea->titulo);
        $tarea->asignado_id = $request->post('asignado_id', $tarea->asignado_id);
        $tarea->cuerpo = $request->post('cuerpo', $tarea->cuerpo);
        $tarea->fecha_expiracion = $request->post('fecha_expiracion', $tarea->fecha_expiracion);
        $tarea->categorias = $request->post('categorias' , $tarea->categorias);

        $this->registrarEnHistorial(
            $tarea->id, 
            'modificado',
            $tarea->titulo,
            $user['id']
        );
        
        $tarea->save();

        return $tarea;
    }

    public function Eliminar(Request $request, $id)
    {
        $user = $request->user;
        $tarea = Tarea::findOrFail($id);

        $this->registrarEnHistorial(
            $tarea->id, 
            'modificado',
            $tarea->titulo,
            $user['id']
        );

        $tarea->delete();
        return response()->json(['deleted' => true]);
    }

    public function AgregarComentario(Request $request, $id)
    {
        $user = $request->user;
        $tarea = Tarea::findOrFail($id);
        
        $comentarios = $tarea->comentarios ?? [];
        $comentarios[] = [
            'usuario_id' => $user['id'],
            'texto' => $request->post('texto'),
            'fecha' => now()->toDateTimeString()
        ];
        
        $tarea->comentarios = $comentarios;
        $tarea->save();

        return $tarea;
    }

    private function registrarEnHistorial(int $tareaId, string $accion, string $titulo, int $usuarioId)
    {
        $datos = [
            'tarea_id' => $tareaId,
            'titulo_tarea' => $titulo,
            'accion' => $accion,
            'usuario_id' => $usuarioId,
        ];

        Http::post($this->historialApiUrl.'/historial/registrar', $datos);
    }

}