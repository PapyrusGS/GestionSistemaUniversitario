CREATE TABLE `usuarios` (
  `idUSuario` int PRIMARY KEY AUTO_INCREMENT,
  `idRol` int,
  `nombre1` varchar(255),
  `nombre2` varchar(255),
  `apellido1` varchar(255),
  `apellido2` varchar(255),
  `ci` varchar(255),
  `fechaNacimiento` datetime,
  `correo` varchar(255),
  `password` varchar(255),
  `fechaRegistro` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `reportes` (
  `idReporte` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` varchar(255),
  `filtros` varchar(255),
  `idUsuario` int,
  `fechaGeneracion` datetime,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `estudiante` (
  `idEstudiante` int PRIMARY KEY AUTO_INCREMENT,
  `idUsuario` int,
  `nombrePadre` varchar(255),
  `apellidoPadre` varchar(255),
  `nombreMadre` varchar(255),
  `apellidoMadre` varchar(255),
  `numeroMadre` varchar(50),
  `numeroPadre` varchar(50)
);

CREATE TABLE `rol` (
  `idRol` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255),
  `descripcion` varchar(255),
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `carrera` (
  `idCarrera` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255),
  `descripcion` varchar(255),
  `fechaRegistro` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `materia` (
  `idMateria` int PRIMARY KEY AUTO_INCREMENT,
  `idCarrera` int,
  `idMateriaPrevia` int,
  `nombre` varchar(255),
  `semestre` int,
  `fechaRegistro` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `estudiante_carrera` (
  `idEstudianteCarrera` int PRIMARY KEY AUTO_INCREMENT,
  `idEstudiante` int,
  `idCarrera` int,
  `fechaRegistro` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `estudiantemateria` (
  `idInscripcion` int PRIMARY KEY AUTO_INCREMENT,
  `idEstudiante` int,
  `idCursoMateria` int,
  `fecha` datetime,
  `estado` varchar(50) DEFAULT 'Inscrito',
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `curso` (
  `idCurso` varchar(100) PRIMARY KEY,
  `capacidad` int,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `horariocurso` (
  `idHorarioCurso` int PRIMARY KEY AUTO_INCREMENT,
  `idCurso` int,
  `idHorario` int,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `horario` (
  `idHorario` int PRIMARY KEY AUTO_INCREMENT,
  `diaSemana` int,
  `horaInicio` time,
  `horaFin` time,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `cursos_materias` (
  `idCursoMateria` int PRIMARY KEY AUTO_INCREMENT,
  `idCurso` int,
  `idMateria` int,
  `idDocente` int,
  `idPeriodo` int,
  `fechaInicio` datetime,
  `fechaFin` datetime,
  `maxInscritos` int,
  `fechaRegistro` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `periodo` (
  `idPeriodo` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fechaInicioSemestre` date,
  `fechaFinSemestre` date,
  `estado` bit NOT NULL DEFAULT 1
);

CREATE TABLE `notas` (
  `idNota` int PRIMARY KEY AUTO_INCREMENT,
  `idInscripcion` int,
  `nota` decimal,
  `fechaRegistro` datetime,
  `fechaActualizacion` datetime,
  `estado` bit,
  `fechaA` datetime,
  `UsuarioA` varchar(255),
  `estadoA` bit
);

CREATE TABLE `auditorias` (
  `IdAuditoria` int PRIMARY KEY AUTO_INCREMENT,
  `TablaNombre` varchar(100),
  `RegistroId` int,
  `Accion` char(1),
  `Campo` varchar(100),
  `ValorAnterior` text,
  `ValorNuevo` text,
  `UsuarioA` int,
  `FechaA` datetime,
  `DireccionIP` varchar(45)
);

ALTER TABLE `TUsuario` ADD FOREIGN KEY (`idRol`) REFERENCES `TRol` (`idRol`);

ALTER TABLE `TReportes` ADD FOREIGN KEY (`idUsuario`) REFERENCES `TUsuario` (`idUSuario`);

ALTER TABLE `TEstudiante` ADD FOREIGN KEY (`idUsuario`) REFERENCES `TUsuario` (`idUSuario`);

ALTER TABLE `TMateria` ADD FOREIGN KEY (`idCarrera`) REFERENCES `TCarrera` (`idCarrera`);

ALTER TABLE `TMateria` ADD FOREIGN KEY (`idMateriaPrevia`) REFERENCES `TMateria` (`idMateria`);

ALTER TABLE `TEstudianteCarrera` ADD FOREIGN KEY (`idEstudiante`) REFERENCES `TEstudiante` (`idEstudiante`);

ALTER TABLE `TEstudianteCarrera` ADD FOREIGN KEY (`idCarrera`) REFERENCES `TCarrera` (`idCarrera`);

ALTER TABLE `TEstudianteMateria` ADD FOREIGN KEY (`idEstudiante`) REFERENCES `TEstudiante` (`idEstudiante`);

ALTER TABLE `TEstudianteMateria` ADD FOREIGN KEY (`idCursoMateria`) REFERENCES `TCursoMateria` (`idCursoMateria`);

ALTER TABLE `THorarioCurso` ADD FOREIGN KEY (`idCurso`) REFERENCES `TCurso` (`idCurso`);

ALTER TABLE `THorarioCurso` ADD FOREIGN KEY (`idHorario`) REFERENCES `THorario` (`idHorario`);

ALTER TABLE `TCursoMateria` ADD FOREIGN KEY (`idCurso`) REFERENCES `TCurso` (`idCurso`);

ALTER TABLE `TCursoMateria` ADD FOREIGN KEY (`idMateria`) REFERENCES `TMateria` (`idMateria`);

ALTER TABLE `TCursoMateria` ADD FOREIGN KEY (`idDocente`) REFERENCES `TUsuario` (`idUSuario`);

ALTER TABLE `TCursoMateria` ADD FOREIGN KEY (`idPeriodo`) REFERENCES `TPeriodo` (`idPeriodo`);

ALTER TABLE `TNota` ADD FOREIGN KEY (`idInscripcion`) REFERENCES `TEstudianteMateria` (`idInscripcion`);

ALTER TABLE `TSAuditoria` ADD FOREIGN KEY (`UsuarioA`) REFERENCES `TUsuario` (`idUSuario`);
