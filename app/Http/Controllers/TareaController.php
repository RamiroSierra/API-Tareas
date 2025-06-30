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
        $tarea = new Tarea();
        $tarea->titulo = $request->post('titulo');
        $tarea->autor_id = $request->post('autor_id');
        $tarea->asignado_id = $request->post('asignado_id');
        $tarea->cuerpo = $request->post('cuerpo');
        $tarea->fecha_expiracion = $request->post('fecha_expiracion');
        $tarea->categorias = $request->post('categorias');
        $tarea->save();

        $this->registrarEnHistorial($tarea->id, 'creacion', [
            'titulo' => $tarea->titulo,
            'autor_id' => $tarea->autor_id
        ]);

        return $tarea;
    }

    public function Modificar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->titulo = $request->post('titulo', $tarea->titulo);
        $tarea->asignado_id = $request->post('asignado_id', $tarea->asignado_id);
        $tarea->cuerpo = $request->post('cuerpo', $tarea->cuerpo);
        $tarea->fecha_expiracion = $request->post('fecha_expiracion', $tarea->fecha_expiracion);
        
        if ($request->has('categorias')) {
            $tarea->categorias = $request->post('categorias');
        }

        $this->registrarEnHistorial($tarea->id, 'actualizacion', [
            'titulo' => $tarea->titulo,
            'usuario_id' => $tarea->autor_id,
        ]);
        
        $tarea->save();

        return $tarea;
    }

    public function Eliminar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $this->registrarEnHistorial($tarea->id, 'eliminacion', [
            'titulo' => $tarea->titulo,
            'usuario_id' => $tarea->autor_id,
        ]);

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

    private function registrarEnHistorial(int $tareaId, string $accion, array $datos)
    {
        try {
            $payload = [
                'tarea_id' => $tareaId,
                'titulo_tarea' => $datos['titulo'],
                'estado_actual' => $accion,
                'usuario_id' => $datos['usuario_id'],
            ];

            Http::post($this->historialApiUrl.'/historial/registrar', $payload);
        } catch (\Exception $e) {
            \Log::error("Error registrando en historial: " . $e->getMessage());
        }
    }

}