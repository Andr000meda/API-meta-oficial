create table if not exists alumnos (
    id int primary key auto_increment,
    nombres varchar(255),
    apellidos varchar(255),
    matricula varchar(255),
    numero_telefono varchar(255),
    numero_seguro_social varchar(255)
);

create table if not exists documentos (
    id int primary key auto_increment,
    id_alumno int,
    nombre varchar(255),
    url varchar(255)
);

create table if not exists sesiones (
    id int primary key auto_increment,
    numero_telefono varchar(255),
    estado varchar(255)
)

alter table documentos add constraint fk_documentos_alumnos foreign key (id_alumno) references alumnos(id);

alter table sesiones add constraint unique_numero_telefono unique (numero_telefono);

alter table alumnos add constraint unique_numero_telefono unique (numero_telefono);