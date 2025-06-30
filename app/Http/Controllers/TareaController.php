<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TareaController extends Controller
{
    public function __construct()
    {
        $this->historialApiUrl = env('HISTORIAL_API_URL', 'http://api-historial.test/api');
    }
    
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
            'creacion', [
            'titulo' => $tarea->titulo,
            $user['id']
        ]);

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
        
        if ($request->has('categorias')) {
            $tarea->categorias = $request->post('categorias');
        }

        $this->registrarEnHistorial(
            $tarea->id, 
            'creacion', [
            'titulo' => $tarea->titulo,
            $user['id']
        ]);
        
        $tarea->save();

        return $tarea;
    }

    public function Eliminar(Request $request, $id)
    {
        $user = $request->user;
        $tarea = Tarea::findOrFail($id);

        $this->registrarEnHistorial(
            $tarea->id, 
            'creacion', [
            'titulo' => $tarea->titulo,
            $user['id']
        ]);

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

    private function registrarEnHistorial(int $tareaId, string $accion, array $datos)
    {
        try {
            $payload = [
                'tarea_id' => $tareaId,
                'titulo_tarea' => $datos['titulo'],
                'accion' => $accion,
                'usuario_id' => $datos['usuario_id'],
            ];

            Http::post($this->historialApiUrl.'/historial/registrar', $payload);
        } catch (\Exception $e) {
            \Log::error("Error registrando en historial: " . $e->getMessage());
        }
    }

}