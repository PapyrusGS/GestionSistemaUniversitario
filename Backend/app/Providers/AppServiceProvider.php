<?php

namespace App\Providers;

use App\Repositories\Contracts\CarreraRepositoryInterface;
use App\Repositories\Contracts\CursoRepositoryInterface;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use App\Repositories\Contracts\HorarioRepositoryInterface;
use App\Repositories\Contracts\MateriaRepositoryInterface;
use App\Repositories\Contracts\NotaRepositoryInterface;
use App\Repositories\Contracts\PeriodoRepositoryInterface;
use App\Repositories\Contracts\RendimientoRepositoryInterface;
use App\Repositories\Contracts\ReporteNotasRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentCarreraRepository;
use App\Repositories\Eloquent\EloquentCursoRepository;
use App\Repositories\Eloquent\EloquentDocenteRepository;
use App\Repositories\Eloquent\EloquentHorarioRepository;
use App\Repositories\Eloquent\EloquentMateriaRepository;
use App\Repositories\Eloquent\EloquentNotaRepository;
use App\Repositories\Eloquent\EloquentPeriodoRepository;
use App\Repositories\Eloquent\EloquentRendimientoRepository;
use App\Repositories\Eloquent\EloquentReporteNotasRepository;
use App\Repositories\Eloquent\EloquentRoleRepository;
use App\Repositories\Eloquent\EloquentStudentRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Eloquent\EloquentProfileRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(CarreraRepositoryInterface::class, EloquentCarreraRepository::class);
        $this->app->bind(MateriaRepositoryInterface::class, EloquentMateriaRepository::class);
        $this->app->bind(CursoRepositoryInterface::class, EloquentCursoRepository::class);
        $this->app->bind(DocenteRepositoryInterface::class, EloquentDocenteRepository::class);
        $this->app->bind(HorarioRepositoryInterface::class, EloquentHorarioRepository::class);
        $this->app->bind(PeriodoRepositoryInterface::class, EloquentPeriodoRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, EloquentStudentRepository::class);
        $this->app->bind(NotaRepositoryInterface::class, EloquentNotaRepository::class);
        $this->app->bind(RendimientoRepositoryInterface::class, EloquentRendimientoRepository::class);
        $this->app->bind(ReporteNotasRepositoryInterface::class, EloquentReporteNotasRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class,EloquentProfileRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
    
}