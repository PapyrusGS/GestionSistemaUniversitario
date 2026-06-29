<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    // Propiedades PHP puras (NO atributos de Eloquent)
    // Se declaran fuera del array $attributes para que Eloquent no las incluya en el SQL.
    protected array $_auditPending  = [];
    protected bool  $_auditIsDelete = false;

    public static function bootAuditable()
    {
        // ─── CREATING: rellenar metadatos automáticos ───────────────────────
        static::creating(function ($model) {
            $userId = Auth::id() ?? null;

            if ($model->isFillable('UsuarioA') && empty($model->UsuarioA)) {
                $model->UsuarioA = $userId;
            }
            if ($model->isFillable('fechaA') && empty($model->fechaA)) {
                $model->fechaA = now();
            }
            if ($model->isFillable('estadoA') && !isset($model->estadoA)) {
                $model->estadoA = 1;
            }
            if ($model->isFillable('estado') && !isset($model->estado)) {
                $model->estado = 1;
            }
        });

        // ─── CREATED: una fila en auditorias por cada columna ───────────────
        static::created(function ($model) {
            $userId    = Auth::id() ?? null;
            $tableName = $model->getTable();
            $pk        = $model->getKeyName();
            $recordId  = (string) $model->$pk;

            foreach ($model->getAttributes() as $key => $value) {
                if (is_null($value)) continue;
                Auditoria::create([
                    'tabla_nombre'   => $tableName,
                    'registro_id'    => $recordId,
                    'accion'         => 'C',
                    'campo'          => $key,
                    'valor_anterior' => null,
                    'valor_nuevo'    => is_scalar($value) ? (string) $value : json_encode($value),
                    'usuario_a'      => $userId,
                    'fecha_a'        => now(),
                    'direccion_ip'   => request()->ip() ?? '127.0.0.1',
                ]);
            }
        });

        // ─── UPDATING: capturar estado ANTES del save ───────────────────────
        // getDirty() y getOriginal() son válidos aquí. En 'updated' ya no.
        static::updating(function ($model) {
            $userId = Auth::id() ?? null;

            // Actualizar metadatos
            if ($model->isFillable('UsuarioA')) {
                $model->UsuarioA = $userId;
            }
            if ($model->isFillable('fechaA')) {
                $model->fechaA = now();
            }

            // Detectar borrado lógico ANTES de guardar (aquí isDirty() funciona)
            $isDelete = false;
            $dirty    = $model->getDirty();

            if (array_key_exists('estado', $dirty) && $dirty['estado'] == 0) {
                $isDelete = true;
            } elseif (array_key_exists('estadoA', $dirty) && $dirty['estadoA'] == 0) {
                $isDelete = true;
            }

            // Guardar snapshot en propiedades PHP (NO en $attributes)
            $pending = [];
            foreach ($dirty as $key => $newValue) {
                if (!$isDelete && in_array($key, ['UsuarioA', 'fechaA', 'updated_at', 'created_at'])) {
                    continue;
                }
                $pending[$key] = [
                    'old' => $model->getOriginal($key),
                    'new' => $newValue,
                ];
            }

            // Asignar a propiedades PHP del trait (no a $attributes de Eloquent)
            $model->_auditPending  = $pending;
            $model->_auditIsDelete = $isDelete;
        });

        // ─── UPDATED: guardar en auditorias usando el snapshot ──────────────
        static::updated(function ($model) {
            $userId    = Auth::id() ?? null;
            $tableName = $model->getTable();
            $pk        = $model->getKeyName();
            $recordId  = (string) $model->$pk;
            $pending   = $model->_auditPending;
            $isDelete  = $model->_auditIsDelete;
            $accion    = $isDelete ? 'D' : 'U';

            foreach ($pending as $key => $values) {
                Auditoria::create([
                    'tabla_nombre'   => $tableName,
                    'registro_id'    => $recordId,
                    'accion'         => $accion,
                    'campo'          => $key,
                    'valor_anterior' => is_scalar($values['old']) ? (string) $values['old'] : json_encode($values['old']),
                    'valor_nuevo'    => is_scalar($values['new']) ? (string) $values['new'] : json_encode($values['new']),
                    'usuario_a'      => $userId,
                    'fecha_a'        => now(),
                    'direccion_ip'   => request()->ip() ?? '127.0.0.1',
                ]);
            }

            // Limpiar el snapshot
            $model->_auditPending  = [];
            $model->_auditIsDelete = false;
        });

        // ─── DELETED: borrado físico real (por si acaso) ────────────────────
        static::deleted(function ($model) {
            $userId    = Auth::id() ?? null;
            $tableName = $model->getTable();
            $pk        = $model->getKeyName();
            $recordId  = (string) $model->$pk;

            foreach ($model->getAttributes() as $key => $value) {
                Auditoria::create([
                    'tabla_nombre'   => $tableName,
                    'registro_id'    => $recordId,
                    'accion'         => 'D',
                    'campo'          => $key,
                    'valor_anterior' => is_scalar($value) ? (string) $value : json_encode($value),
                    'valor_nuevo'    => null,
                    'usuario_a'      => $userId,
                    'fecha_a'        => now(),
                    'direccion_ip'   => request()->ip() ?? '127.0.0.1',
                ]);
            }
        });
    }
}
